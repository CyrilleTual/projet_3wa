<?php

declare(strict_types=1);

namespace Controllers;

class RendersController
{
    private ?string $view;

    public function __construct($view = null, $data = null, $errors = null)
    {

        $this->view = isset($_GET['view']) ? $_GET['view'] : $view;
        $this->data = $data;
        $this->errors = $errors;
        $this->handleRequest();
    }

    public function getView(): ?string
    {
        return $this->view;
    }
    public function getData(): ?string
    {
        return $this->data;
    }

    private function handleRequest()
    {

        if ($this->getView() === null) {
            $this->render('homePage');
        } else {
            switch ($this->PageNotFound()) {
                case true:
                    $this->render($this->view, $this->data, $this->errors);
                    break;
                case false:
                    $this->render('page404');
                    break;
                default:
                    $this->render('homePage');
                    break;
            }
        }
    }

    private function render($view, $data = null, $errors = null): void
    {

        require "public/template/layout.phtml";
    }

    private function PageNotFound(): bool
    {
        if (file_exists("public/views/{$this->getView()}.phtml"))
            return true;
        else
            return false;
    }
}
