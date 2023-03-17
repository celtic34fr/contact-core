<?php

namespace Celtic34fr\ContactCore\MySQLiEntity;

class MSICliInfos
{
    private ?int $id = null;
    private ?string $nom = null;
    private ?string $prenom = null;
    private ?string $telephone = null;
    private ?int $client_id;

    private ?MSIClientele $client = null;

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
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string|null $nom
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return string|null
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * @param string|null $prenom
     */
    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     */
    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return int|null
     */
    public function getClientId(): ?int
    {
        return $this->client_id;
    }

    /**
     * @param int|null $client_id
     */
    public function setClientId(?int $client_id): void
    {
        $this->client_id = $client_id;
    }

    /**
     * @return MSIClientele|null
     */
    public function getClient(): ?MSIClientele
    {
        return $this->client;
    }

    /**
     * @param MSIClientele|null $client
     */
    public function setClient(?MSIClientele $client): void
    {
        $this->client = $client;
        $this->client_id = $client->getId();
    }

}
