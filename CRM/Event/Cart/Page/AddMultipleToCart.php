<?php
// Change below
class CRM_Event_Cart_Page_AddMultipleToCart extends CRM_Core_Page {
  function run() {

    // Add for loop here - Duplicate first? Make this the copy beond default file?
    //  Load sub events from id and loop
    //  Don't want to return to same page
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);

    $params = array(
      'version' => 3,
      'sequential' => 1,
      'parent_event_id' => $this->_id,
    );

    $events = civicrm_api('Event', 'get', $params);

    foreach ($events['values'] as $subEvent) {

      $transaction = new CRM_Core_Transaction();

      // This needs to be changed
      if (!CRM_Core_Permission::check('register for events')) {
        CRM_Core_Error::fatal(ts('You do not have permission to register for this event'));
      }
      if (!CRM_Core_Permission::event(CRM_Core_Permission::VIEW, $subEvent['id'])) {
        CRM_Core_Error::fatal(ts('You cannot register for an event you do not have permission to view'));
      }

      $cart = CRM_Event_Cart_BAO_Cart::find_or_create_for_current_session();
      $event_in_cart = $cart->add_event($subEvent['id']);

      $url=CRM_Utils_System::url('civicrm/event/view_cart');
      CRM_Utils_System::setUFMessage(ts("<b>%1</b> has been added to your cart. <a href='%2'>View your cart.</a>", array(1 => $event_in_cart->event->title,2 => $url)));

      $transaction->commit();
    }

    return CRM_Utils_System::redirect($_SERVER['HTTP_REFERER']);

    }
}



