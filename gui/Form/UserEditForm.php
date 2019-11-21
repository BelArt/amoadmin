<?php

namespace App\User\Gui\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Класс UserEditForm
 */
class UserEditForm extends Form
{
    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        $this
            ->add(
                (new Text('name', ['placeholder' => 'Логин']))
                    ->setLabel('Логин')
                    ->addValidator(new PresenceOf(['message' => 'Не стоит оставлять это пустым']))
            )
            ->add(
                (new Text('email', ['placeholder' => 'Электронная почта']))
                    ->setLabel('Электронная почта')
                    ->addValidators(
                        [
                            new PresenceOf(['message' => 'Не стоит оставлять это пустым']),
                            new Email(['message' => 'Это не похоже на электронную почту']),
                        ]
                    )
            );

        if (!$entity->_id) {
            $this->add(
                (new Password('pass', ['placeholder' => 'Пароль']))
                    ->setLabel('Пароль')
                    ->addValidator(new PresenceOf(['message' => 'Не стоит оставлять это пустым']))
            );
        }

        $this->add(
            new Submit('submit', ['class' => 'btn btn-primary', 'value' => 'Сохранить'])
        );
    }

    /**
     *
     */
    public function afterValidation()
    {
        $this->getEntity()->name = strtolower($this->getEntity()->name);
    }
}
