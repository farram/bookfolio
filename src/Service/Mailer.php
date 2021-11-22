<?php

namespace App\Service;

use Liip\ImagineBundle\Service\FilterService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    protected $mailer;

    public function __construct(MailerInterface $mailer, FilterService $filterService)
    {
        $this->mailer = $mailer;
        $this->filterService = $filterService;
    }

    public function sendEmailInbox($book, $message, $user)
    {
        $email = new TemplatedEmail();
        $email->from(new Address($user->getEmail(), 'Bookfolio'))
            ->to($book->getEmail())
            ->htmlTemplate('emails/inbox_new.html.twig')
            ->context([
                'user' => $book->getUser(),
                'name' => $user->getFullname(),
                'society' => null,
                'emailAddress' => $user->getEmail(),
                'phone' => null,
                'message' => $message,
            ])
            ->subject('Nouveau message de ' . $user->getFullname() . '');
        $this->mailer->send($email);
    }

    public function sendEmailComment($datas)
    {
        $email = new TemplatedEmail();
        $email->from(new Address($datas['user']->getEmail(), 'Bookfolio'))
            ->to($datas['book']->getEmail())
            ->htmlTemplate('emails/comment_image.html.twig')
            ->context($this->contextEmailComment($datas))
            ->subject('Bookfolio | ' . $datas['user']->getFullname() . ' a commenté une de vos photos');
        $this->mailer->send($email);
    }

    public function sendEmailAnswerComment($datas)
    {
        $email = new TemplatedEmail();
        $email->from(new Address($datas['user']->getEmail(), 'Bookfolio'))
            ->to($datas['book']->getEmail())
            ->htmlTemplate('emails/answer_comment_image.html.twig')
            ->context($this->contextEmailComment($datas))
            ->subject('Bookfolio | ' . $datas['user']->getFullname() . ' a répondu à votre commentaire');
        $this->mailer->send($email);
    }

    public function contextEmailComment($datas)
    {
        $context = [
            'comment' => $datas['comment'],
            'image' => [
                'id' => $datas['image']->getId(),
                'path' => $this->filterService->getUrlOfFilteredImage($datas['image']->getImagePath(), 'thumb_large'),
                'title' => $datas['image']->getTitle(),
                'galleryId' => $datas['image']->getGallery()->getId(),
            ],
            'sender' => [
                'url' => '',
                'fullname' => $datas['user']->getFullname(),
                'avatar' => $this->filterService->getUrlOfFilteredImage($datas['user']->getAvatar(), 'avatar'),
                'profession' => $datas['user']->getProfession()->getTitle(),
                'location' => $datas['user']->getAddress()->getFullAddress(),
            ],
        ];

        return $context;
    }

    public function sendEmailNewFollower($user, $owner)
    {
        $context = [
            'receiver' => [
                'url' => '',
                'fullname' => $user->getFullname(),
                'firstname' => $user->getFirstname(),
                'avatar' => $this->filterService->getUrlOfFilteredImage($user->getAvatar(), 'avatar'),
                'profession' => $user->getProfession()->getTitle(),
                'location' => $user->getAddress()->getFullAddress(),
            ],
            'sender' => [
                'url' => '',
                'fullname' => $owner->getFullname(),
                'firstname' => $owner->getFirstname(),
                'avatar' => $this->filterService->getUrlOfFilteredImage($owner->getAvatar(), 'avatar'),
                'profession' => $owner->getProfession()->getTitle(),
                'location' => $owner->getAddress()->getFullAddress(),
                'book' => [
                    'name' => $owner->getBook()->getName(),
                ],
            ],
        ];

        $email = new TemplatedEmail();
        $email->from(new Address($owner->getEmail(), 'Bookfolio'))
            ->to($user->getEmail())
            ->htmlTemplate('emails/new_follower.html.twig')
            ->context($context)
            ->subject('Bookfolio | ' . $context['sender']['fullname'] . ' a commencé à vous suivre');
        $this->mailer->send($email);
    }

    public function sendEmailReactionAnnonce($reaction)
    {
        $context = [
            'reaction' => $reaction,
            'comment' => $reaction->getComment(),
            'receiver' => [
                'fullname' => $reaction->getAnnonce()->getUser()->getFullname(),
                'firstname' => $reaction->getAnnonce()->getUser()->getFirstname(),
                'avatar' => $this->filterService->getUrlOfFilteredImage($reaction->getAnnonce()->getUser()->getAvatar(), 'avatar'),
                'profession' => $reaction->getAnnonce()->getUser()->getProfession()->getTitle(),
                'location' => $reaction->getAnnonce()->getUser()->getAddress()->getFullAddress(),
            ],
            'sender' => [
                'fullname' => $reaction->getUser()->getFullname(),
                'firstname' => $reaction->getUser()->getFirstname(),
                'avatar' => $this->filterService->getUrlOfFilteredImage($reaction->getUser()->getAvatar(), 'avatar'),
                'profession' => $reaction->getUser()->getProfession()->getTitle(),
                'location' => $reaction->getUser()->getAddress()->getFullAddress(),
                'book' => [
                    'name' => $reaction->getUser()->getBook()->getName(),
                ],
            ],
        ];

        $email = new TemplatedEmail();
        $email->from(new Address($reaction->getUser()->getEmail(), 'Bookfolio'))
            ->to($reaction->getAnnonce()->getUser()->getEmail())
            ->htmlTemplate('emails/add_reaction_annonce.html.twig')
            ->context($context)
            ->subject('Bookfolio | ' . $context['sender']['fullname'] . ' a répondu à votre annonce');
        $this->mailer->send($email);
    }

    public function sendEmailPurchaseSuccess($user, $subscription, $current_subscription, $invoice)
    {
        $context = [
            'user' => $user,
            'subscription' => [
                'price' => ($subscription->getPrice() / 100) . '€',
                'name' => $subscription->getName(),
                'date' => date('j-m-Y', $current_subscription['current_period_start']),
                'invoice' => [
                    'number' => $invoice['number'],
                    //'current_period_end' => $user->getSubscription()->getBillingPeriodEndsAt(),
                    'current_period_end' => '',
                    'pdf' => $invoice['invoice_pdf'],
                    'hosted_invoice_url' => $invoice['hosted_invoice_url'],
                ],
            ],
        ];

        $email = new TemplatedEmail();
        $email->from(new Address('info@book-folio.fr', 'Bookfolio'))
            ->to($user->getEmail())
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context($context)
            ->subject('Confirmation d\'abonnement');
        $this->mailer->send($email);
    }
}
