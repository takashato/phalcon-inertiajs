<?php


namespace Takashato\Inertia;


use Phalcon\Di\Injectable;

class Response extends Injectable
{
    protected array $viewData = [];
    protected $component;
    protected $props;
    protected $version;

    public function __construct($component, $props, $version = null)
    {
        $this->component = $component;
        $this->props = $props;
        $this->version = $version;
    }

    public function with($key, $value = null): Response
    {
        if (is_array($key)) {
            $this->props = array_merge($this->props, $key);
        } else {
            $this->props[$key] = $value;
        }

        return $this;
    }

    public function withViewData($key, $value = null): Response
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    public function getProps(): array
    {
        $partialData = $this->request->getHeader('X-Inertia-Partial-Data');
        $only = array_filter(
            explode(',', $partialData ? $partialData : '')
        );

        $partialComponent = $this->request->getHeader('X-Inertia-Partial-Component');
        $props = ($only && ($partialComponent ? $partialComponent : '') === $this->component)
            ? array_only($this->props, $only)
            : $this->props;

        array_walk_recursive($props, static function (&$prop) {
            $prop = closure_call($prop);
        });

        $page = [
            'component' => $this->component,
            'props' => $props,
            'url' => $this->request->getURI() !== '/' ? '/' . $this->request->getURI() : '/',
            'version' => $this->version,
        ];

        return $this->makeProps($page);
    }

    private function makeProps($page): array
    {
        $inertia = $this->request->getHeader('X-Inertia');

        if ($inertia) {
            $this->response->setHeader('Vary', 'Accept');
            $this->response->setHeader('X-Inertia', 'true');
            $this->response->setHeader('Content-Type', 'application/json');

            return $page;
        }

        return $this->viewData + ['page' => $page];
    }
}
