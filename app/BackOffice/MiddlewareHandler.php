<?php

namespace Ovoform\BackOffice;

class MiddlewareHandler{
    public function handle($assignedMiddleware = [])
    {
        foreach ($assignedMiddleware as $middleware) {
			if (array_key_exists($middleware,ovoform_system_instance()->middleware)) {
				$middlewareName = ovoform_system_instance()->middleware[$middleware];
				$this->callMiddleware($middlewareName);
			}
		}
    }

    public function filterGlobalMiddleware(){
		foreach (ovoform_system_instance()->globalMiddleware as $middleware) {
			$this->callMiddleware($middleware);
		}
	}

    private function callMiddleware($middleware)
	{
		$middlewareClass = new $middleware;
		$middlewareClass->filterRequest();
	}
}