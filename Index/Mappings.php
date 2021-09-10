<?php

declare(strict_types=1);

namespace Sigmie\Base\Index;

use Exception;
use Sigmie\Base\Analysis\DefaultAnalyzer;
use Sigmie\Base\Analysis\SimpleAnalyzer;
use Sigmie\Base\Contracts\CustomAnalyzer;
use Sigmie\Base\Contracts\Mappings as MappingsInterface;
use Sigmie\Base\Contracts\Type;
use Sigmie\Base\Mappings\Properties;
use Sigmie\Base\Mappings\Types\Boolean;
use Sigmie\Base\Mappings\Types\Date;
use Sigmie\Base\Mappings\Types\Number;
use Sigmie\Base\Mappings\Types\Text;
use Sigmie\Support\Collection;
use Sigmie\Support\Contracts\Collection as ContractsCollection;

class Mappings implements MappingsInterface
{
    protected Properties $properties;

    protected CustomAnalyzer $defaultAnalyzer;

    public function __construct(
        ?CustomAnalyzer $defaultAnalyzer = null,
        ?Properties $properties = null,
    ) {
        $this->defaultAnalyzer = $defaultAnalyzer ?: new DefaultAnalyzer();
        $this->properties = $properties ?: new Properties();
    }

    public function properties(): Properties
    {
        return $this->properties;
    }

    public function analyzers(): Collection
    {
        return $this->properties->textFields()->filter(fn (Type $field) => $field instanceof Text)
            ->map(fn (Text $field) => $field->analyzer() ? $field->analyzer() : $this->defaultAnalyzer);
    }

    public function toRaw(): array
    {
        return [
            'properties' => $this->properties->toRaw(),
            // 'dynamic_templates' => $this->dynamicTemplate()
        ];
    }

    public static function fromRaw(array $data, ContractsCollection $analyzers): Mappings
    {
        $analyzers = $analyzers->mapToDictionary(
            fn (CustomAnalyzer $analyzer) => [$analyzer->name() => $analyzer]
        )->toArray();
        $defaultAnalyzer = $analyzers['default'];

        $fields = [];

        if (isset($data['properties']) === false) {
            return new DynamicMappings($defaultAnalyzer);
        }

        foreach ($data['properties'] as $fieldName => $value) {
            $field = match ($value['type']) {
                'search_as_you_type' => (new Text($fieldName))->searchAsYouType(),
                'text' => (new Text($fieldName))->unstructuredText(),
                'integer' => (new Number($fieldName))->integer(),
                'float' => (new Number($fieldName))->float(),
                'boolean' => new Boolean($fieldName),
                'date' => new Date($fieldName),
                //TODO test completion from raw
                'completion' => (new Text($fieldName))->completion(),
                default => throw new Exception('Field ' . $value['type'] . ' couldn\'t be mapped')
            };

            if ($field instanceof Text && !isset($value['analyzer'])) {
                $value['analyzer'] = 'default';
            }

            if ($field instanceof Text && isset($value['analyzer'])) {
                $analyzerName = $value['analyzer'];

                $analyzer = match ($analyzerName) {
                    'simple' => new SimpleAnalyzer,
                    default => $analyzers[$analyzerName]
                };

                $field->withAnalyzer($analyzer);
            }

            $fields[$fieldName] = $field;
        }

        $properties = new Properties($fields);

        return new static(
            defaultAnalyzer: $defaultAnalyzer,
            properties: $properties,
        );
    }
}
