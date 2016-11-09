<?php

namespace MyModule;

use \Context;
use \Customer;
use \Mail;
use \Module;
use \MyModule;
use \Language;
use \Tools;
use \MyModule\Core\Translatable;
use \Validate;

/**
 * Class Email
 * @package MyModule
 */
abstract class Email
{
    /** Allows using $this->l('Text to translate') */
    use Translatable;

    /** @var Customer|string|null */
    protected $to = null;

    /** @var string|null */
    protected $from = null;

    /** @var array|null */
    protected $attachments = null;

    /** @var Language|null */
    protected $language = null;

    /** @var string */
    protected $templateName;

    /** @var MyModule */
    protected $module;

    /** @var Context */
    protected $context;

    /** @var Language */
    protected $contextLanguage;

    /**
     * Email constructor.
     */
    public function __construct()
    {
        // \MyModule\Email\NewCommentEmail -> new_comment
        $bits = explode('\\', get_class($this));
        $this->templateName = Tools::toUnderscoreCase(substr(end($bits), 0, -5));

        // Because need module path to set email template path.
        // Also, we may need some dependencies from the module to compile templates.
        $this->module = Module::getInstanceByName('mymodule');

        // We will be switching languages, so we'll need to tamper with the current context
        $this->context = Context::getContext();
        $this->contextLanguage = $this->context->language;
    }

    /**
     * Getter for email subject.
     * Subject must be set inside the extended class, no public setters!
     * Use $this->l('Translatable subject') to return a subject.
     * Language may be switched for this email, so the subject must stay inside the function.
     *
     * @return string
     */
    abstract protected function subject();

    /**
     * Setter/getter for recipient email address and name.
     *
     * @param Customer|string $emailOrCustomer
     *
     * @return $this
     * @throws Exception
     */
    public function to($emailOrCustomer)
    {
        if (!is_string($emailOrCustomer) && !($emailOrCustomer instanceof Customer)) {
            throw new Exception(sprintf(
                $this->l('Expected an instance of \\Customer or an email string, got: [%s]'),
                get_class($emailOrCustomer)
            ));
        }

        $this->to = $emailOrCustomer;

        return $this;
    }

    /**
     * Setter/getter for sender email address and name.
     *
     * @param string $emailAddress
     *
     * @return $this
     */
    public function from($emailAddress)
    {
        $this->from = $emailAddress;
        return $this;
    }

    /**
     * Setter/getter for an array of files to be attached to the emails.
     *
     * @param array $attachments
     *
     * @return $this
     */
    public function withAttachments(array $attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * Sets email language
     *
     * @param Language $language
     *
     * @return $this
     */
    public function in(Language $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Sends the email.
     *
     * @return bool
     * @throws Exception
     */
    public function send()
    {
        // Do we need to send an email in a specific language? Temporarily switch context.
        if (null !== $this->language) {
            $this->context->language = $this->language;
        }

        // Converts an array of attachment file paths to what Mail::Send understands.
        $files = null;
        if (null !== $this->attachments) {
            $files = array_map(function ($filePath) {
                return array(
                    'content' => file_get_contents($filePath),
                    'name'    => pathinfo($filePath, PATHINFO_FILENAME),
                    'mime'    => mime_content_type($filePath),
                );
            }, $this->attachments);
        }

        // Recipient information
        if ($this->to instanceof Customer) {
            $to     = $this->to->email;
            $toName = $this->to->firstname.' '.$this->to->lastname;
        } else {
            $to     = $this->to;
            $toName = null;
        }

        if (!Validate::isEmail($to)) {
            throw new Exception(sprintf($this->l('Invalid recipient email address: [%s]'), $this->to));
        }

        // Variables in email templates need to have keys in this format: {$key}.
        $variables = array();
        foreach ($this->variables() as $key => $value) {
            $variables['{'.$key.'}'] = $value;
        }

        $templatePath = $this->module->getLocalPath().'mails/';
        $fromName = null;
        $smtp     = null;

        $sent = (bool)Mail::Send(
            $this->context->language->id,
            $this->templateName,
            $this->subject(),
            $variables,
            $to,
            $toName,
            $this->from,
            $fromName,
            $files,
            $smtp,
            $templatePath
        );

        // Restore the context language
        $this->context->language = $this->contextLanguage;

        return $sent;
    }

    /**
     * Returns variables for email template.
     * Context language may be switched, so variables need to be generated by a function.
     *
     * @return array
     */
    protected function variables()
    {
        return array();
    }
}
