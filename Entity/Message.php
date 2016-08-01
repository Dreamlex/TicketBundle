<?php
namespace Dreamlex\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SellMMO\Sonata\MediaBundle\Entity\Media; //TODO Зависимость
use SellMMO\Sonata\UserBundle\Entity\User; //TODO Зависимость
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="ticket_message")
 * @ORM\HasLifecycleCallbacks
 */
class Message
{
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_INFORMATION_REQUESTED = 'information_requested';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    private static $priorities = [
        self::PRIORITY_LOW => self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM => self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH => self::PRIORITY_HIGH,
    ];

    private static $statuses = [
        self::STATUS_OPEN => self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
        self::STATUS_INFORMATION_REQUESTED => self::STATUS_INFORMATION_REQUESTED,
        self::STATUS_ON_HOLD => self::STATUS_ON_HOLD,
        self::STATUS_RESOLVED => self::STATUS_RESOLVED,
        self::STATUS_CLOSED => self::STATUS_CLOSED,
    ];
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\Valid()
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $priority;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @Assert\Valid()
     */
    private $media;

    /**
     *
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Dreamlex\TicketBundle\Entity\Ticket", inversedBy="messages")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", nullable=false)
     */
    private $ticket;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Message
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return Message
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Message
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set media
     *
     * @param Media $media
     *
     * @return Message
     */
    public function setMedia(Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Message
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public static function getPriorities()
    {
        return self::$priorities;
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    public static function getStatuses($prefix = '')
    {
        if ($prefix !== '') {
            return array_map(function ($a) use ($prefix) {
                return $prefix.$a;
            }, self::$statuses);
        }

        return self::$statuses;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        // if null, then new ticket
        if (\is_null($this->getTicket()->getUser())) {
            $this->getTicket()->setUser($this->getUser());
        }
        $this->getTicket()->setLastUser($this->getUser());


        $this->getTicket()->setLastMessageAt($this->getCreatedAt());
        $this->getTicket()->setPriority($this->getPriority());

        // if ticket not closed, then it'll be set to null
        if (\is_null($this->getStatus())) {
            $this->setStatus($this->getTicket()->getStatus());
        } else {
            $this->getTicket()->setStatus($this->getStatus());
        }
    }

    /**
     * Set ticket
     *
     * @param Ticket $ticket
     *
     * @return Message
     */
    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (empty($this->getMedia()) && empty($this->getMessage())) {
            $context->buildViolation('ticket.validate.content')
                ->atPath('message')
                ->addViolation();
            $context->buildViolation('ticket.validate.content')
                ->atPath('media.binaryContent')
                ->addViolation();
        }
    }
}
