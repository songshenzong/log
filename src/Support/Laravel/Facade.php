<?php

namespace Songshenzong\Log\Support\Laravel;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {
	
	protected static function getFacadeAccessor() {
		return 'songshenzong';
	}
}
