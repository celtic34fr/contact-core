<?php

namespace Celtic34fr\ContactCore\Traits;

trait FromErrorsTrait
{

    private function getErrors($form): array
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrors($child);
            }
        }
        return $errors;
    }

}
