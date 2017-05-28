<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="tsuramis")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TsuramiRepository")
 *
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="region_tsuramis")
 */
class Tsurami
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     * @var string
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 127)
     * @var string
     */
    private $text;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }
}
