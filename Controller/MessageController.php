<?php

namespace FOS\MessageBundle\Controller;

use FOS\MessageBundle\Deleter\Deleter;
use FOS\MessageBundle\FormFactory\NewThreadMessageFormFactory;
use FOS\MessageBundle\FormFactory\ReplyMessageFormFactory;
use FOS\MessageBundle\FormHandler\NewThreadMessageFormHandler;
use FOS\MessageBundle\FormHandler\ReplyMessageFormHandler;
use FOS\MessageBundle\ModelManager\ThreadManagerInterface;
use FOS\MessageBundle\Provider\ProviderInterface;
use FOS\MessageBundle\Search\Finder;
use FOS\MessageBundle\Search\QueryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractController
{
    private ProviderInterface $provider;

    private ReplyMessageFormFactory $replyFormFactory;

    private ReplyMessageFormHandler $replyFormHandler;

    private NewThreadMessageFormFactory $newThreadFormFactory;

    private NewThreadMessageFormHandler $newThreadFormHandler;

    private Deleter $deleter;

    private ThreadManagerInterface $threadManager;

    private QueryFactory $query;

    private Finder $finder;

    public function __construct(ReplyMessageFormFactory $replyFormFactory, ReplyMessageFormHandler $replyFormHandler, NewThreadMessageFormFactory $newThreadFormFactory, NewThreadMessageFormHandler $newThreadFormHandler, Deleter $deleter, ThreadManagerInterface $threadManager, QueryFactory $query, Finder $finder)
    {
        $this->replyFormFactory = $replyFormFactory;
        $this->replyFormHandler = $replyFormHandler;
        $this->newThreadFormFactory = $newThreadFormFactory;
        $this->newThreadFormHandler = $newThreadFormHandler;
        $this->deleter = $deleter;
        $this->threadManager = $threadManager;
        $this->query = $query;
        $this->finder = $finder;
    }

    /**
     * Displays the authenticated participant inbox.
     *
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->provider->getInboxThreads();

        return $this->render('@FOSMessage/Message/inbox.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays the authenticated participant messages sent.
     *
     * @return Response
     */
    public function sentAction()
    {
        $threads = $this->provider->getSentThreads();

        return $this->render('@FOSMessage/Message/sent.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays the authenticated participant deleted threads.
     *
     * @return Response
     */
    public function deletedAction()
    {
        $threads = $this->provider->getDeletedThreads();

        return $this->render('@FOSMessage/Message/deleted.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays a thread, also allows to reply to it.
     *
     * @param string $threadId the thread id
     *
     * @return Response
     */
    public function threadAction($threadId)
    {
        $thread = $this->provider->getThread($threadId);
        $form = $this->replyFormFactory->create($thread);

        if ($message = $this->replyFormHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('fos_message_thread_view', array(
                'threadId' => $message->getThread()->getId(),
            )));
        }

        return $this->render('@FOSMessage/Message/thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread,
        ));
    }

    /**
     * Create a new message thread.
     *
     * @return Response
     */
    public function newThreadAction()
    {
        $form = $this->newThreadFormFactory->create();

        if ($message = $this->newThreadFormHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('fos_message_thread_view', array(
                'threadId' => $message->getThread()->getId(),
            )));
        }

        return $this->render('@FOSMessage/Message/newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData(),
        ));
    }

    /**
     * Deletes a thread.
     *
     * @param string $threadId the thread id
     *
     * @return RedirectResponse
     */
    public function deleteAction($threadId)
    {
        $thread = $this->provider->getThread($threadId);
        $this->deleter->markAsDeleted($thread);
        $this->threadManager->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
    }

    /**
     * Undeletes a thread.
     *
     * @param string $threadId
     *
     * @return RedirectResponse
     */
    public function undeleteAction($threadId)
    {
        $thread = $this->provider->getThread($threadId);
        $this->deleter->markAsUndeleted($thread);
        $this->threadManager->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox.
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->query->createFromRequest();
        $threads = $this->finder->find($query);

        return $this->render('@FOSMessage/Message/search.html.twig', array(
            'query' => $query,
            'threads' => $threads,
        ));
    }

    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }
}
