<?php
class Moxy_MoxyMagazine_Model_Api extends Mage_Api_Model_Resource_Abstract
{

	public function listCollection($sessionId)
    {

		$server_name = $_SERVER['SERVER_NAME'];

        $session_path = Mage::getBaseDir('session');
        $file= $session_path . '/sess_' . $sessionId;
        $contents=file_get_contents($file);

        if (!$contents) {
            Mage::log('File '. $file .' not found');
            return $data;
        }

        session_start();
        session_decode($contents);

        Mage::getSingleton('core/session', array('name' => 'frontend'));

        // Customer Session
        $session = Mage::getSingleton('customer/session');

        $userId = $_SESSION['customer']['id'];
		$customer = $_SESSION['customer'];

        $customer = $_SESSION['customer'];
		$wishlists = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer)->getCollection()->addFilter('visibility', 1)->setOrder('updated_at', 'DESC');
		//$wishlists = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer)->getCollection()->addFilter('visibility', 1)->setOrder('updated_at', 'DESC');
		/*
		Mage::log(print_r($wishlists), true);
		foreach ($wishlists as $wishlist) {

			Mage::log(">>>>>>>>");
			Mage::log(print_r($wishlist, true));
		}
		 */
		#$wishlists = Mage::getModel('wishlist/wishlist')->getCollection()->addFilter('visibility', 1)->setOrder('updated_at', 'DESC');
		return $wishlists;
		/*
        $wishlist = Mage::getModel('wishlist/wishlist');

        $wishlist->setCustomerId($customerId)
            ->setName($wishlistName)
            ->setVisibility($visibility)
            ->generateSharingCode()
            ->save();

        # Create rewrite, first check the availability
        $slug = Mage::getModel('catalog/product_url')->formatUrlKey($wishlistName);
        $routeAvailable = $this->checkCollectionRouteAvailable($wishlist->getId(), $slug);

        if ($routeAvailable) {

            # Create new route rewrite
            $this->createNewCollectionRewrite($wishlist->getId(), $slug);

        } else {

            # If not available, try another else

            $error = false;
            for ($i = 1;;$i++) {
                $check = $slug . '-' . substr(uniqid(), 7);
                if ($this->checkCollectionRouteAvailable($wishlist->getId(), $check)) break;
                if ($i == 100) {
                    $error = true;
                    break;
                }
            }

            if ($error) {
                throw new Exception("Error Processing Request", 1);
            }

            # Create new route rewrite
            $this->createNewCollectionRewrite($wishlist->getId(), $slug);

        }

        # Check if rewrite still available
        if (! $routeAvailable) {
            throw new Exception("Username not available"); # Route not available
        }

        return $wishlist;
		 */

    }

	public function getQuoteCart($sessionId) {

		$server_name = $_SERVER['SERVER_NAME'];

        $session_path = Mage::getBaseDir('session');
        $file= $session_path . '/sess_' . $sessionId;
        $contents=file_get_contents($file);

        if (!$contents) {
            Mage::log('File '. $file .' not found');
            return [];
        }

        session_start();
        session_decode($contents);

        Mage::getSingleton('core/session', array('name' => 'frontend'));

        // Customer Session
        $session = Mage::getSingleton('customer/session');

        $userId = $_SESSION['customer']['id'];
		$customer = $_SESSION['customer'];
		Mage::log($customer);
		$quoteCollection = Mage::getModel('sales/quote')->getCollection();
		$quoteCollection->addFieldToFilter('customer_id', $userId);
		$quoteCollection->addOrder('updated_at');
		$quote = $quoteCollection->getLastItem();
		$products = array();
		foreach ($quote->getAllItems() as $item) {
			//echo var_dump($quoteItem);

			$product = $item->getProduct();//Mage::getModel('catalog/product')->load($item->getItemId());

			array_push($products, array(
				"id" => $product->getId(),
				"name" => $product->getName(),
				"image" => (string)Mage::helper('catalog/image')->init($product, 'thumbnail'),
				"url" => $product->getProductUrl(),
				"qty" => $item->getQty(),
				"price" => $item->getPrice(),
				"item_id" => $item->getId()
			));
		}
		//Mage::log($products);
		//return $quote['entity_id'];
		$quoteData= $quote->getData();
		$grandTotal=$quoteData['grand_total'];
		return array(
			"entity_id" => $quote['entity_id'],
			"cart_items" => $products,
			"cart_total" => $grandTotal
		);

		/*
		$quote = Mage::getSingleton('sales/quote')->loadByCustomer($customer);
		Mage::log(var_dump($quote));
		Mage::log( $quote->getId());
		Mage::log($quote['entity_id']);
		return $quote['entity_id'];
		 */

	}

    public function getSessionData($sessionId)
    {
        /**
        Return:
            1. customer_id
            2. customer_firstname
            3. customer_lastname
            4. customer_email
            5. quote_id
            6. wishlist_count
            7. total_qty
            8. subtotal
            9a. cart.product_id
            9b. cart.product_name
            9c. cart.product_price
            9d. cart.product_qty
            9e. cart.product_url
            9f. cart.product_image
        **/

        $time_start = microtime(true);

        $data = [];
        $server_name = $_SERVER['SERVER_NAME'];

        $cache_type = (string) Mage::getConfig()->getNode('global/session_save');
        if ('cache_type' == $sessionId) return $cache_type;

        if ('db' == $cache_type) {
            $redis_session = new Cm_RedisSession_Model_Session();
            $contents = $redis_session->read($sessionId);
        } else {
            $session_path = Mage::getBaseDir('session');
            $file = $session_path . '/sess_' . $sessionId;
            $contents = file_get_contents($file);
        }

        if (!$contents) {
            Mage::log('Session ' . $cache_type . ' ' . $sessionId . ' is not found');
            return $data;
        }

        session_start();
        session_decode($contents);

        // Customer Session
        $customerId = $_SESSION['customer']['id'];
        if ($customerId) {
            $data["customer_id"] = $customerId;
            $data["wishlist_count"] = $_SESSION['customer']['wishlist_item_count'];
            $checkout = $_SESSION["checkout"];
            $quote_id = $checkout['quote_id_1'];
            $data['quote_id'] = $quote_id;
            Mage::getSingleton('checkout/session')->setQuoteId($quote_id);
            $cart = Mage::getModel('sales/quote')->getCollection()
                        ->addFieldToFilter('entity_id', $quote_id)
                        ->getFirstItem();
            $data["subtotal"] = $cart->getData()["subtotal"];
            $data["customer_firstname"] = $cart->getData()["customer_firstname"];
            $data["customer_lastname"] = $cart->getData()["customer_lastname"];
            $data["customer_email"] = $cart->getData()["customer_email"];
            $data["total_qty"] = count($cart->getAllVisibleItems());
            foreach ($cart->getAllVisibleItems() as $item) {
                $product = array();
                $productId = $item->getProductId();
                $itemProduct = $item->getProduct();
                $product["product_id"] = $productId;
                $product["product_name"] = $itemProduct->getName();
                $product["product_price"] = $itemProduct->getPrice();
                $product["product_qty"] = $item->getQty();
                $product["product_url"] = Mage::getResourceSingleton('catalog/product')->getAttributeRawValue($productId, 'url_key', Mage::app()->getStore());

                $productModel = Mage::getModel('catalog/product')->load($productId);
                $product["product_image"] = (string) Mage::helper('catalog/image')->init($productModel, 'thumbnail')->resize('46x46');

                $data["cart"][] = $product;
            }
        }

        $time_end = microtime(true);

        $data['execute_time'] = $time_end - $time_start;

        return $data;
    }
}
