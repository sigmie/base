<?php

namespace Sigma\Document;

use Sigma\Element;
use Sigma\Exception\NotImplementedException;
use Sigma\Mapping\Types\Boolean;
use Sigma\Mapping\Types\Text;

class Document extends Element
{
    /**
     * Index that the Document belogs to
     *
     * @var string
     */
    protected $index = [Text::class];

    /**
     * Document identifier
     *
     * @var string
     */
    protected $id;

    /**
     * Bolean indicator if the document
     * should contain indexed_at and
     * updated_at timestamps
     *
     * @var boolean
     */
    protected $_timestamps = true;

    /**
     * Element class type
     *
     * @var string
     */
    protected $type = self::class;

    /**
     * Default datetime format
     *
     * @var string
     */
    protected $_dateFormat = 'YYYY-MM-DD';

    /**
     * Indicator if the active behavior
     * should be disabled
     *
     * @var boolean
     */
    protected $_disableActive = false;

    /**
     * Active indicator
     *
     * @var bool
     */
    protected $active = [Boolean::class, true];

    public function fill()
    {
        throw new NotImplementedException();
    }

    public function save()
    {
        throw new NotImplementedException();
    }
}
