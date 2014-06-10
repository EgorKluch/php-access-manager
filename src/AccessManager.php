<?php
/**
 * @author EgorKluch (EgorKluch@gmail.com)
 * @date: 10.06.2014
 *
 */

namespace EgorKluch;

class AccessManager {
  public function __construct() {
    $this->prepareHandlers = array(
      'default' => function ($action, $args) { return $args; }
    );

    $this->handlers = array(
      'default' => function ($action, $args) { return true; }
    );
  }

  /**
   * Добавляет к действию $action функцию предварительной обработки $prepareHandle
   * Функция должна возвращать массив данных, на основе которых будет определяться есть ли у пользователя
   * права на текущее действие
   * Если обработчик не определен, вызывается обработчик default
   *
   * @param string $action
   * @param callable $prepareHandler
   */
  public function prepareHandle ($action, $prepareHandler) {
    $this->_regHandler($action, $prepareHandler, $this->prepareHandlers);
  }

  /**
   * Подписывает обработчик проверки доступа пользователя на событие $action
   * Обработчик принимает на вход $action и результат работы обработчика prepareHandle $args
   * Возвращает булево значение
   * Если обработчик не определен, вызывается обработчик default
   *
   * @param string $action
   * @param callable $handler
   */
  public function handle ($action, $handler) {
    $this->_regHandler($action, $handler, $this->handlers);
  }

  /**
   * Проверяет, есть ли у пользователя права на соверщение действия $action
   *
   * @param string $action
   * @param array $args
   * @return bool
   */
  public function hasAccess ($action, $args = array()) {
    $prepareAction = $action;
    if (!array_key_exists($prepareAction, $this->prepareHandlers)) {
      $prepareAction = 'default';
    }
    $args = $this->prepareHandlers[$prepareAction]($action, $args);

    if (!array_key_exists($action, $this->handlers)) {
      $action = 'default';
    }
    return $this->handlers[$action]($action, $args);
  }

  protected function _regHandler ($action, $handler, &$container) {
    if (is_array($action)) {
      foreach ($action as $act) {
        $this->_regHandler($act, $handler, $container);
      }
      return;
    }

    if (is_callable($action)) {
      $handler = $action;
      $action = 'default';
    }

    if (!$action) $action = 'default';

    $container[$action] = $handler;
  }

  /**
   * @var array
   */
  protected $handlers;

  /**
   * @var array
   */
  protected $prepareHandlers;
}