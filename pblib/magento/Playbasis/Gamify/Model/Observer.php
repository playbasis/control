<?php


require_once('pblib/playbasis.php');

/**
 * Gamify Observer
 *
 * @category   Playbasis
 * @package    Playbasis_Gamify
 * @author     eddie c. <eddie@playbasis.com>
 */
class Playbasis_Gamify_Model_Observer
{
	private $pb = null;
	const api_key = 'abc';
	const api_secret = 'abcde';
	const addToCartAction = 'like';
	const addToWishlistAction = 'want';
	const userIdPrefix = 'mgusr_';
	const userNamePrefix = 'mgusr_';
	const defaultProfileImage = 'https://www.pbapp.net/images/default_profile.jpg';
	const defaultLastName = 'magento';

	public function __construct()
	{
		$this->pb = new Playbasis();
	}
	public function auth(Varien_Event_Observer $observer)
	{
		$result = $this->pb->auth(self::api_key, self::api_secret);
		assert($result);
	}
	public function register(Varien_Event_Observer $observer)
	{
		//$account = $observer->getEvent()->getData('account_controller');
		$customer = $observer->getEvent()->getData('customer');
		$result = $this->registerCustomer($customer);
		return $this;
	}
	public function login(Varien_Event_Observer $observer)
	{
		$customer = $observer->getEvent()->getData('customer');
		$id = $this->getPlaybasisUserId($customer->getId());
		$result = $this->pb->login($id);
		if(!$result['success'] && $result['error_code'] == '0200') //user doesn't exist
		{
			//register and login again
			$this->registerCustomer($customer);
			$result = $this->pb->login($id);
		}
		return $this;
	}
	public function logout(Varien_Event_Observer $observer)
	{
		$customer = $observer->getEvent()->getData('customer');
		$id = $this->getPlaybasisUserId($customer->getId());
		$result = $this->pb->logout($id);
		return $this;
	}
	public function addToCart(Varien_Event_Observer $observer)
	{
		//$product = $observer->getEvent()->getData('product');
		$result = $this->triggerActionOnLoggedInCustomer(self::addToCartAction);
		return $this;
	}
	public function addToWishlist(Varien_Event_Observer $observer)
	{
		$wishlist = $observer->getEvent()->getData('wishlist');
		//$product = $observer->getEvent()->getData('product');
		//$item = $observer->getEvent()->getData('item');
		$id = $this->getPlaybasisUserId($wishlist->getCustomerId());
		$result = $this->pb->rule($id, self::addToWishlistAction);
		return $this;
	}

	private function registerCustomer($customer)
	{
		$firstname = $customer->getFirstname();
		$lastname = $customer->getLastname();
		$email = $customer->getEmail();
		$id = $customer->getId();
		return $this->pb->register(
			$this->getPlaybasisUserId($id), 
			self::userNamePrefix . $email,
			$email, 
			self::defaultProfileImage, 
			array(
				'first_name' => $firstname,
				'last_name' => $lastname
			)
		);
	}
	private function triggerActionOnLoggedInCustomer($action)
	{
		$session = $this->getCustomerSession();
		if($session->isLoggedIn())
		{
			$customer = $session->getCustomer();
			$id = $this->getPlaybasisUserId($customer->getId());
			return $this->pb->rule($id, $action);
		}
		return null;
	}
	private function getPlaybasisUserId($userId)
	{
		return self::userIdPrefix . $userId;
	}
	private function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
