# Req(uest) Fuck(er)

Fucking with the `FormRequest` class for the `App\Http\Requests\Request` abstract class.

### Requirements
* PHP > 7
* Laravel HTTP > 5.1


### Installing
```json
{
	"require": {
		"prjkt/repofuck": "dev-master"
	}
}
```
\*\* *I'll have to ask for forgiveness for installing from dev-master*


### Usage

```php
<?php

namespace App\Http\Requests;

class SampleRequest extends Request
{
	protected $rules = [

		'post' => [
			'username' => 'required',
			'password' => 'required'
		],

		'put' => [
			'id' => 'required|exists:users',
			'username' => 'required',
			'password' => 'min:6'
		],

	];
}
```

### Juicy stuff

Better used with [repofuck](https://github.com/prjkt/repofuck)


w/ repofuck
`app/Repositories/UsersRepository`

```php
<?php

namespace App\Repositories;

use Prjkt\Component\Repofuck\Repofuck as Repository;

class UsersRepository extends Repository
{
	protected $resources = [
		\App\Entities\User::class
	];
}
```

`app/Http/Controllers/UsersController`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleRequest;
use App\Repositories\UsersRepository;

class UsersController extends Controller
{
	protected $users;

	public function __construct(UsersRepository $users)
	{
		$this->users = $users;
	}

	public postUser(SampleRequest $request)
	{
		// only mass assign variables based on the validation keys present
		$user = $this->create($request->all(), $request->getKeys());
	}
}
```