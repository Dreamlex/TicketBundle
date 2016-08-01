<?php
namespace Dreamlex\TicketBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SellMMO\Sonata\UserBundle\Entity\User; //TODO Зависимость
use Symfony\Component\Validator\Constraints as Assert;

/**
 * /**
 * @ORM\Entity(repositoryClass="TicketRepository")
 * @GRID\Source(columns="id, category_subject, status_priority, created_updated, category.title, subject, status, priority, lastMessageAt, createdAt, lastUser.id, isRead")
 * @GRID\Column(id="category_subject", type="join" ,columns={"subject","category.title"})
 * @GRID\Column(id="status_priority", type="join" ,columns={"status","priority"})
 * @GRID\Column(id="created_updated", type="join" ,columns={"lastMessageAt","createdAt"})
 * @ORM\Table(name="ticket")
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(filterable=false)
     */
    private $id;

    /**
     * @GRID\Column( type="datetime", filter="select", visible=false )
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lastMessageAt;

    /**
     * @GRID\Column(filterable=false, sortable=false, visible=false)
     * @Assert\NotBlank(message = "ticket.validate.subject")
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $subject;

    /**
     * @GRID\Column(operators={"eq"}, defaultOperator="eq", selectMulti="true", visible=false, operatorsVisible=false, filter="select", selectFrom="values",
     *     selectExpanded=true, values={
     *          "open"="open",
     *          "closed"="closed"
     * })
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @GRID\Column(operators={"eq"}, defaultOperator="eq", selectMulti="true", visible=false, operatorsVisible=false, filter="select", selectFrom="values",
     *     selectExpanded=true, values={
     *          "high"="high",
     *          "medium"="medium",
     *          "low"="low"
     * })
     * @ORM\Column(type="string", nullable=false)
     */
    private $priority;

    /**
     * @GRID\Column(filter="input",operators={"btwe"},operatorsVisible=false, defaultOperator="btwe",  visible=false,)
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Grid\Column(visible=false)
     */
    private $isRead;

    /**
     * @ORM\OneToMany(targetEntity="Dreamlex\TicketBundle\Entity\Message", mappedBy="ticket", cascade={"all"})
     * @Assert\Type(type="object", message="The value {{ value }} is not a valid {{ type }}")
     * @Assert\Valid()
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="\Dreamlex\TicketBundle\Model\UserInterface", inversedBy="tickets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="?id_from_external_entity?", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="SellMMO\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="last_user_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(field="lastUser.id", visible=false)
     */
    private $lastUser;

    /**
     * @ORM\ManyToOne(targetEntity="Dreamlex\TicketBundle\Entity\Category", inversedBy="tickets")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     * @GRID\Column( field="category.title", visible=false, operators={"like","isNull"}, defaultOperator="like", operatorsVisible=true, selectMulti=true,  filter="select", selectFrom="source")
     */
    private $category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->messages = new ArrayCollection();
        $this->isRead = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSubject();
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Ticket
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
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
     * Get lastMessageAt
     *
     * @return \DateTime
     */
    public function getLastMessageAt()
    {
        return $this->lastMessageAt;
    }

    /**
     * Set lastMessageAt
     *
     * @param \DateTime $lastMessageAt
     *
     * @return \DateTime
     */
    public function setLastMessageAt($lastMessageAt)
    {
        $this->lastMessageAt = $lastMessageAt;

        return $this->lastMessageAt;
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
     * Set status
     *
     * @param string $status
     *
     * @return Ticket
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Set priority
     *
     * @param string $priority
     *
     * @return Ticket
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Ticket
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     *
     * @return Ticket
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Add messages
     *
     * @param Message $message
     *
     * @return Ticket
     */
    public function addMessage(Message $message)
    {
        $message->setTicket($this);
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param Message $message
     */
    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
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
     * Set user
     *
     * @param User $user
     *
     * @return Ticket
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get lastUser
     *
     * @return User
     */
    public function getLastUser()
    {
        return $this->lastUser;
    }

    /**
     * Set lastUser
     *
     * @param User $lastUser
     *
     * @return Ticket
     */
    public function setLastUser(User $lastUser)
    {
        $this->lastUser = $lastUser;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Ticket
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }
}
