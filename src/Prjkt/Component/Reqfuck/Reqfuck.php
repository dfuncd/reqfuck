<?php

namespace Prjkt\Component\Reqfuck;

use Illuminate\Foundation\Http\FormRequest;

abstract class Reqfuck extends FormRequest
{
	/**
	 * Validation rules
	 *
	 * @var array
	 */
	protected $rules = [];

	/**
	 * Get the parsed method
	 *
	 * @return string
	 */
	public function parsedMethod() : string
	{
		return strtolower($this->method());
	}

	/**
	 * OVERRIDE!
	 *
	 * This will call `$this->resolveValidationRules` depending on the request method,
	 * returns all of the rules by default
	 * --
	 * Get the validator instance for the request.
	 *
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function getValidatorInstance() : \Illuminate\Contracts\Validation\Validator
	{
		$factory = $this->container->make(\Illuminate\Contracts\Validation\Factory::class); // Originally as ValidationFactory

		if (method_exists($this, 'validator')) {
			return $this->container->call([$this, 'validator'], compact('factory'));
		}
		return $factory->make(
			$this->validationData(), $this->container->call([$this, 'rules'], [$this->getRuleKeys()]), $this->messages(), $this->attributes()
		);
	}

	/**
	 * Return the validation rule set from a pre-set
	 *
	 * @param array $params
	 * @return array
	 */
	public function rules(array $params = [], $command = 'only') : array
	{
		$rules = array_key_exists($this->parsedMethod(), $this->rules) ? $this->rules[$this->parsedMethod()] : [];

		return count($params) > 0 ? call_user_func($this->getCommand($command), [$rules, $params]): $rules;
	}

	/**
	 * Return the validation rules depending on the request method
	 *
	 * @return array
	 */
	public function getRuleKeys() : array
	{
		return array_key_exists($this->parsedMethod(), $this->rules) ? array_keys($this->rules[$this->parsedMethod()]): [];
	}

	/**
	 * Gets the appropriate command to run
	 *
	 * @param string $command
	 * @throws \Exception
	 * @return string
	 */
	public function getCommand(string $command) : string
	{
		$commands = [
			'only' => 'array_only',
			'pluck' => 'array_pluck'
		];

		if ( ! array_key_exists($command, $commands) ) {
			throw \Exception('Command not found');
		}

		return $command;
	}

	/**
	 * Default response
	 * 
	 * --
	 * @param array $errors
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function response(array $errors) : \Illuminate\Http\JsonResponse
	{
		$errors = array_merge(['code' => 422, 'data' => $errors]);

		return response()->json($errors);
	}
}
