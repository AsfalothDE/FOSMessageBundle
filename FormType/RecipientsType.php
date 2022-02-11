<?php

namespace FOS\MessageBundle\FormType;

use FOS\MessageBundle\DataTransformer\RecipientsDataTransformer;
use FOS\MessageBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of RecipientsType.
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class RecipientsType extends AbstractType
{
    private RecipientsDataTransformer $recipientsTransformer;

    /**
     * @param RecipientsDataTransformer $transformer
     */
    public function __construct(RecipientsDataTransformer $transformer)
    {
        $this->recipientsTransformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->recipientsTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected recipient does not exist',
        ));
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'recipients_selector';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return LegacyFormHelper::getType(TextType::class);
    }

    /**
     * @deprecated To remove when supporting only Symfony 3
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
