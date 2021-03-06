<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     * @param string $language
     */
    public function nameIt($name, $language);

    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeIt($description, $languageCode);

    /**
     * @param string $amount
     */
    public function specifyAmount($amount);

    /**
     * @param string $name
     */
    public function chooseZone($name);

    /**
     * @param string $name
     */
    public function chooseCalculator($name);
}
