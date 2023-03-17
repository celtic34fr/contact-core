<?php

namespace Celtic34fr\ContactCore\MySQLiEntity;

class MSIClientele
{
    private ?int $id = null;
    private ?string $courriel = null;
    private ?string $type = null;
    private array $cliInfos;
    private array $events;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getCourriel(): ?string
    {
        return $this->courriel;
    }

    /**
     * @param string|null $courriel
     */
    public function setCourriel(?string $courriel): void
    {
        $this->courriel = $courriel;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getCliInfos(): array
    {
        return $this->cliInfos;
    }

    /**
     * @param array $cliInfos
     */
    public function setCliInfos(array $cliInfos): void
    {
        $this->cliInfos = $cliInfos;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param array $events
     */
    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

}
