<?php 


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	// Motif Product registration
	class WC_Product_motifcal extends WC_Product {

		public function __construct( $product ) {

			$this->product_type = 'motifcal';

			parent::__construct( $product );

		}
	}
?>