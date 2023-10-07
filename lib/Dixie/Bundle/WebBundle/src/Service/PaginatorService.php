<?php

declare(strict_types=1);

namespace Talav\WebBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PaginatorService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private Environment $twig,
        private RequestStack $request,
        private $class = null,
        private $page = 1,
        private $limit = 10,
        private $criteria = [],
        private $parameters = [null => null],
        private $order = ['id' => 'ASC'],
        private $type = 'crud',
        private $method = 'findBy'
    ){}

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function display($style = null)
    {
        $this->twig->display('@TalavWeb/_partials/pagination.html.twig', [
            'page'  => $this->page,
            'pages' => $this->getPages(),
            'route' => $this->request->getCurrentRequest()->attributes->get('_route'),
            'sort' => $this->order,
            'parameters' => $this->parameters,
            'start' => $this->getStart(),
            'end' => $this->getEnd(),
            'type' => $this->type,
            'style' => $style
        ]);
    }

    public function getData()
    {
        $offset = $this->page * $this->limit - $this->limit;
        $repo = $this->manager->getRepository($this->class);
        $method = $this->method;

        return $repo->$method($this->criteria,$this->order,$this->limit, $offset);
    }

    public function getPages()
    {
        $repo = $this->manager->getRepository($this->class);
        $method = $this->method;
        $total = count($repo->$method($this->criteria));

        return ceil($total / $this->limit);
    }

    public function getStart(): int
    {
        if ($this->page > 2) {
            $start = $this->page - 2;
        } elseif ($this->page == 2) {
            $start = $this->page - 1;
        } else {
            $start = $this->page;
        }

        return $start;
    }

    public function getEnd(): int
    {
        if ($this->page < $this->getPages() - 1) {
            $end = $this->page + 2;
        } elseif ($this->page == $this->getPages() - 1) {
            $end = $this->page + 1;
        } else {
            $end = $this->page;
        }

        return $end;
    }

    public function setMethod($method): PaginatorService
    {
        $this->method = $method;
        return $this;
    }

    public function setType($type): PaginatorService
    {
        $this->type = $type;
        return $this;
    }

    public function setOrder($order): PaginatorService
    {
        $this->order = $order;
        return $this;
    }

    public function setCriteria($criteria): PaginatorService
    {
        $this->criteria = $criteria;
        return $this;
    }

    public function setParameters($parameters): PaginatorService
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function setClass($class): PaginatorService
    {
        $this->class = $class;
        return $this;
    }

    public function setPage($page): PaginatorService
    {
        $this->page = $page;
        return $this;
    }

    public function setLimit($limit): PaginatorService
    {
        $this->limit = $limit;
        return $this;
    }
}
