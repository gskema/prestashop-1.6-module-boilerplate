<?php

namespace MyModule\Email;

use \MyModule\Email;

use \ObjectModel;

/**
 * Class NewCommentEmail
 * @package MyModule\Email
 */
class NewCommentEmail extends Email
{
    /** @var ObjectModel */
    protected $comment;

    /**
     * NewCommentEmail constructor.
     *
     * @param ObjectModel $comment
     */
    public function __construct(ObjectModel $comment)
    {
        parent::__construct();
        $this->comment = $comment;
    }

    /**
     * Returns email subject.
     *
     * @return string
     */
    protected function subject()
    {
        return sprintf(
            $this->l('New comment: %s').
            $this->comment->title
        );
    }

    /**
     * Returns email variables
     *
     * @return array
     */
    protected function variables()
    {
        // Use can even use $this->module->... here
        return [
            'comment' => $this->comment->comment,
        ];
    }
}
