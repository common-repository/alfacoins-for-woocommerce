<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

defined('ABSPATH') || exit;

final class WC_ALFAcoins_Blocks_Support extends AbstractPaymentMethodType
{
  protected $name = 'alfacoins';

  public function initialize()
  {
    $this->settings = get_option('woocommerce_alfacoins_settings', []);
  }

  public function is_active()
  {
    return filter_var($this->get_setting('enabled', true), FILTER_VALIDATE_BOOLEAN);
  }

  private function get_enable_for_virtual()
  {
    return filter_var($this->get_setting('enable_for_virtual', true), FILTER_VALIDATE_BOOLEAN);
  }

  public function get_payment_method_script_handles()
  {
    $asset_path = plugin_dir_path(__FILE__) . 'build' . DIRECTORY_SEPARATOR . 'block.asset.php';
    $version = get_plugin_data(__FILE__)['Version'];
    $dependencies = [];

    if (file_exists($asset_path)) {
      $asset        = require $asset_path;
      $version      = is_array($asset) && isset($asset['version'])
        ? $asset['version']
        : $version;
      $dependencies = is_array($asset) && isset($asset['dependencies'])
        ? $asset['dependencies']
        : $dependencies;
    }

    wp_register_script(
      'alfacoins-blocks-integration',
      plugins_url('../build/block.js', __FILE__),
      $dependencies,
      $version,
      true
    );

    return ['alfacoins-blocks-integration'];
  }

  public function get_payment_method_data()
  {
    return [
      'title'                    => $this->get_setting('title'),
      'description'              => $this->get_setting('description'),
      'enableForVirtual'         => $this->get_enable_for_virtual(),
      'supports'                 => $this->get_supported_features(),
      'icons'                    => $this->get_icons()
    ];
  }

  private function get_icons()
  {
		$icons_src = [];
    $icons = ['bitcoin', 'ethereum', 'erc20usdt', 'dai', 'usdc', 'xrp', 'tron', 'smartchain', 'litecoin', 'dogecoin', 'bitcoincash', 'cardano', 'polkadot', 'stellar', 'cosmos'];
    foreach ($icons as $ico) {
			if ($this->get_setting('enable_' . $ico . '_icon', true) == 'yes') {
				$icons_src[$ico] = [
					'src' => plugin_dir_url(__FILE__) . '../assets/img/c/' . $ico . '.png',
					'alt' => __('Pay with Cryptocurrency', 'alfacoins'),
				];
			}
    }
    return $icons_src;
  }
}
