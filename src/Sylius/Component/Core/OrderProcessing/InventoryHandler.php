<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\Collection;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Inventory\InventoryUnitTransitions;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryHandler implements InventoryHandlerInterface
{
    /**
     * @var InventoryOperatorInterface
     */
    protected $inventoryOperator;

    /**
     * @var OrderItemUnitFactoryInterface
     */
    protected $orderItemUnitFactory;

    /**
     * @var StateMachineFactoryInterface
     */
    protected $stateMachineFactory;

    /**
     * @param InventoryOperatorInterface $inventoryOperator
     * @param OrderItemUnitFactoryInterface $orderItemUnitFactory
     * @param StateMachineFactoryInterface $stateMachineFactory
     */
    public function __construct(
        InventoryOperatorInterface $inventoryOperator,
        OrderItemUnitFactoryInterface $orderItemUnitFactory,
        StateMachineFactoryInterface $stateMachineFactory
    ) {
        $this->inventoryOperator = $inventoryOperator;
        $this->orderItemUnitFactory = $orderItemUnitFactory;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function holdInventory(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $quantity = $this->applyTransition($item->getUnits(), InventoryUnitTransitions::SYLIUS_HOLD);

            $this->inventoryOperator->hold($item->getVariant(), $quantity);
        }
    }

    /**
     * @param Collection $units
     * @param string $transition
     *
     * @return int
     */
    protected function applyTransition(Collection $units, $transition)
    {
        $quantity = 0;

        foreach ($units as $unit) {
            $stateMachine = $this->stateMachineFactory->get($unit, InventoryUnitTransitions::GRAPH);
            if ($stateMachine->can($transition)) {
                $stateMachine->apply($transition);
                ++$quantity;
            }
        }

        return $quantity;
    }
}
