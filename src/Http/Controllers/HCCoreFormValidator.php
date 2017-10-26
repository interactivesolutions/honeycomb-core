<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

/**
 * Class HCCoreFormValidator
 * @package InteractiveSolutions\HoneycombCore\Http\Controllers
 */
abstract class HCCoreFormValidator
{
    use ValidatesRequests;

    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * Editable record id
     *
     * @var
     */
    protected $id;

    /**
     * CoreFormValidator constructor.
     *
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request ?: request();
    }

    /**
     * Validate form
     *
     * @throws \Exception
     */
    public function validateForm()
    {
        $validator = $this->getValidationFactory()->make(
            $this->requestData(),
            $this->rules()
        );

        if ($validator->fails()) {
            throw new \Exception($this->formatErrors($validator->errors()));
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract protected function rules();

    /**
     * Format request data
     *
     * @return array
     */
    protected function requestData()
    {
        return $this->request->all();
    }

    /**
     * Determinate method type (i.e. POST, PUT)
     * @return string
     */
    protected function methodType()
    {
        return $this->request->method();
    }

    /**
     * Set editable record id
     *
     * @param string $id
     * @return $this
     */
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Must return string!!
     *
     * @param MessageBag $errors
     * @return mixed
     */
    protected function formatErrors(MessageBag $errors)
    {
        $output = '';

        foreach ($errors->messages() as $error) {
            foreach ($error as $message) {
                $output .= $message . "\r\n";
            }
        }

        return $output;
    }
}
