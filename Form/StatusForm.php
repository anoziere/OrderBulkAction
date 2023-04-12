<?php

namespace OrderBulkAction\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Thelia\Core\Thelia;
use Thelia\Form\BaseForm;
use Thelia\Model\OrderStatusQuery;

class StatusForm extends BaseForm
{

    protected function buildForm()
    {
        $status = OrderStatusQuery::create()->find();

        $locale = $this->getRequest()->getSession()->getAdminEditionLang()->getLocale();

        $statusList = [];
        /** @var \Thelia\Model\OrderStatus $statu */
        foreach ($status as $statu) {
            if(version_compare(Thelia::THELIA_VERSION, '2.5.0', '<')) {
                $statusList[$statu->getId()] = $statu->setLocale($locale)->getTitle();
            } else {
                $statusList[$statu->setLocale($locale)->getTitle()] = $statu->getId();
            }

        }
        $this->formBuilder
            ->add('order_bulk_action_order_ids', HiddenType::class, [
                'attr' => [
                    'id' => 'order_bulk_action_order_ids'
                ]
            ])
            ->add('order_bulk_action_order_status', ChoiceType::class, [
                'choices' => $statusList,
                'label' => $this->translator->trans('Change status for selection:')
            ])
        ;
    }

}
