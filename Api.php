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
        $customerId = $_SESSION["customer"]["id"];
        if ($customerId) {
            $data["customer_id"] = $customerId;
            $data["wishlist_count"] = $_SESSION["customer"]["wishlist_item_count"];
            $checkout = $_SESSION["checkout"];
            $quote_id = $checkout["quote_id_1"];
            $data["quote_id"] = $quote_id;
            $cart = Mage::getModel('sales/quote')->getCollection()
                        ->addFieldToFilter('entity_id', $quote_id)
                        ->getFirstItem();
            $items = $cart->getAllVisibleItems();
            $cartData = $cart->getData();
            $data["subtotal"] = $cartData["subtotal"];
            $data["customer_firstname"] = $cartData["customer_firstname"];
            $data["customer_lastname"] = $cartData["customer_lastname"];
            $data["customer_email"] = $cartData["customer_email"];
            $data["total_qty"] = count($items);
            $store_id = Mage::app()->getStore();
            foreach ($items as $item) {
                $product = array();
                $productId = $item->getProductId();
                $itemProduct = $item->getProduct();
                $productSKU = $item->getSku();
                $productName = $itemProduct->getName();
                $product["product_id"] = $productId;
                $product["product_sku"] = $productSKU;
                $product["product_qty"] = $item->getQty();
                $product["product_url"] = Mage::getResourceSingleton('catalog/product')->getAttributeRawValue($productId, 'url_key', $store_id);

                $productModel = Mage::getModel('catalog/product')->load($productId);
                $productImage = (string) Mage::helper('catalog/image')->init($productModel, 'thumbnail')->resize('150x150');

                $totalPrice = $itemProduct->getPrice();
                $productType = $productModel->getTypeId();
                $product["product_type"] = $productType;
                if ($productType == "configurable") {
                    $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($productModel);
                    $simple_collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
                    foreach($simple_collection as $simple_product){
                        if ($productSKU == $simple_product->getSku()) {
                            $productName = $simple_product->getName();
                            $totalPrice = $simple_product->getPrice();
                            $productImage = (string) Mage::helper('catalog/image')->init($simple_product, 'thumbnail')->resize('150x150');
                            break;
                        }
                    }
                }
                if ($productType == "bundle") {
                    if($itemProduct->getFinalPrice()) {
                        $totalPrice = (string)$itemProduct->getFinalPrice();
                    } else if ($itemProduct->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                        $optionCol= $itemProduct->getTypeInstance(true)
                                            ->getOptionsCollection($itemProduct);
                        $selectionCol= $itemProduct->getTypeInstance(true)
                                               ->getSelectionsCollection(
                            $itemProduct->getTypeInstance(true)->getOptionsIds($itemProduct),
                            $itemProduct
                        );
                        $optionCol->appendSelections($selectionCol);
                        $price = $itemProduct->getPrice();

                        foreach ($optionCol as $option) {
                            if($option->required) {
                                $selections = $option->getSelections();
                                $minPrice = min(array_map(function ($s) {
                                                return $s->price;
                                            }, $selections));
                                if($itemProduct->getSpecialPrice() > 0) {
                                    $minPrice *= $itemProduct->getSpecialPrice()/100;
                                }

                                $price += round($minPrice,2);
                            }
                        }
                        $totalPrice = (string)$price;
                    } else {
                        $totalPrice = (string)0;
                    }
                }

                $product["product_name"] = $productName;
                $product["product_price"] = $totalPrice;
                $product["product_image"] = $productImage;

                $options = $itemProduct->getTypeInstance(true)->getOrderOptions($itemProduct);
                $result = array();
                if ($options)
                {
                    if (isset($options['options']))
                    {
                      $result = array_merge($result, $options['options']);
                    }
                    if (isset($options['additional_options']))
                    {
                      $result = array_merge($result, $options['additional_options']);
                    }
                    if (!empty($options['attributes_info']))
                    {
                      $result = array_merge($options['attributes_info'], $result);
                    }
                }
                $product["attributes"] = $result;
                $data["cart"][] = $product;
            }
        }
        $time_end = microtime(true);
        session_destroy(session_id());
        $data["execution_time"] = $time_end - $time_start;

        return $data;
    }
}
