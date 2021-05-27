<?php


namespace Takashato\Inertia;

// https://github.com/amiranagram/inertia-codeigniter-4/blob/master/src/Factory.php

use Phalcon\Di\Injectable;
use Phalcon\Http\ResponseInterface;

class Factory extends Injectable
{
    protected array $sharedProps = [];
    protected string $rootView = 'app';
    protected mixed $version;

    /**
     * @param string $name
     */
    public function setRootView(string $name): void
    {
        $this->rootView = $name;
    }

    public function share($key, $value = null): void
    {
        if (is_array($key)) {
            $this->sharedProps = [...$this->sharedProps, ...$key];
        } else {
            array_set($this->sharedProps, $key, $value);
        }
    }

    public function getShared($key = null): array
    {
        if ($key) {
            return array_get($this->sharedProps, $key);
        }

        return $this->sharedProps;
    }

    public function version($version): void
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return (string)closure_call($this->version);
    }

    public function render($component, $props = [])
    {
        $props = (new Response(
            $component,
            array_merge($this->sharedProps, $props),
            $this->getVersion()
        ))->getProps();

        return $this->view->render($this->rootView, $props);
    }

    public function app($page): string
    {
        return '<div id="app" data-page="' . htmlentities(json_encode($page)) . '"></div>';
    }

    public function redirect($uri): ResponseInterface
    {
        return $this->response->redirect($uri, false, 303);
    }

//    public function location($url)
//    {
//        return BaseResponse::make('', 409, ['X-Inertia-Location' => $url]);
//    }
}
