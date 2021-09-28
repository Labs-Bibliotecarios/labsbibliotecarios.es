<?php

/**
 * Glossary
 *
 * @package   Glossary
 * @author    Codeat <support@codeat.co>
 * @copyright 2020
 * @license   GPL 2.0+
 * @link      https://codeat.co
 */

namespace Glossary\Engine;

use Glossary\Engine;

/**
 * Plugin Name Initializer
 */
class Initialize {

	/**
	 * List of class to initialize.
	 *
	 * @var array
	 */
	public $classes = array();

	/**
	 * List of class to not load on free.
	 *
	 * @var array
	 */
	protected $pro_classes = array( 'ACF', 'Css_Customizer', 'Term_Content', 'Search' ); // phpcs:ignore

	/**
	 * Instance of this G_Is_Methods.
	 *
	 * @var object
	 */
	protected $is = null;

	/**
	 * Composer autoload file list.
	 *
	 * @var \Composer\Autoload\ClassLoader
	 */
	private $composer;

	/**
	 * The Constructor that load the entry classes
	 *
	 * @param \Composer\Autoload\ClassLoader $composer Composer autoload output.
	 * @since 2.0
	 */
	public function __construct( \Composer\Autoload\ClassLoader $composer ) {
		$this->is       = new Engine\Is_Methods;
		$this->composer = $composer;

		$this->get_classes( 'Internals' );
		$this->get_classes( 'Integrations' );

		if ( $this->is->request( 'rest' ) ) {
			$this->get_classes( 'Rest' );
		}

		if ( $this->is->request( 'backend' ) ) {
			$this->get_classes( 'Backend' );
		}

		if ( $this->is->request( 'frontend' ) ) {
			$this->get_classes( 'Frontend' );
		}

		$this->load_classes();
	}

	/**
	 * Initialize all the classes.
	 *
	 * @since 2.0
	 */
	private function load_classes() {
		$this->classes = \apply_filters( 'glossary_classes_to_execute', $this->classes );

		foreach ( $this->classes as $class ) {
			try {
				$temp = new $class;
				$temp->initialize();
			} catch ( \Throwable $err ) {
				\do_action( 'glossary_initialize_failed', $err );

				if ( WP_DEBUG ) {
					throw new \Exception( $err->getMessage() );
				}
			}
		}
	}

	/**
	 * Based on the folder loads the classes automatically using the Composer autoload to detect the classes of a Namespace.
	 *
	 * @param string $namespace Class name to find.
	 * @since 2.0
	 * @return array Return the classes.
	 */
	private function get_classes( string $namespace ) {
		$prefix    = $this->composer->getPrefixesPsr4();
		$classmap  = $this->composer->getClassMap();
		$namespace = 'Glossary\\' . $namespace;

		// In case composer has autoload optimized
		if ( isset( $classmap[ 'Glossary\\Engine\\Initialize' ] ) ) {
			$classes = \array_keys( $classmap );

			foreach ( $classes as $class ) {
				if ( \gt_fs()->is_not_paying() ) {
					foreach ( $this->pro_classes as $pro_class ) {
						if ( \substr_compare( $class, $pro_class, -\strlen( $pro_class ) ) === 0 ) {
							continue 2;
						}
					}
				}

				if ( 0 !== \strncmp( (string) $class, $namespace, \strlen( $namespace ) ) ) {
					continue;
				}

				$this->classes[] = $class;
			}

			return $this->classes;
		}

		$namespace .= '\\';

		// In case composer is not optimized
		if ( isset( $prefix[ $namespace ] ) ) {
			$folder    = $prefix[ $namespace ][0];
			$php_files = $this->scandir( $folder );
			$this->find_classes( $php_files, $folder, $namespace );

			if ( !WP_DEBUG ) {
				\wp_die( \esc_html__( 'Glossary is on production environment with missing `composer dumpautoload -o` that will improve the performance on autoloading itself.', GT_TEXTDOMAIN ) );
			}

			return $this->classes;
		}

		return $this->classes;
	}

	/**
	 * Get php files inside the folder/subfolder that will be loaded.
	 * This class is used only when Composer is not optimized.
	 *
	 * @param string $folder Path.
	 * @since 2.0
	 * @return array List of files.
	 */
	private function scandir( string $folder ) {
		$temp_files = \scandir( $folder );
			$files  = array();

		if ( \is_array( $temp_files ) ) {
			$files = $temp_files;
		}

		return \array_diff( $files, array( '..', '.', 'index.php' ) );
	}

	/**
	 * Load namespace classes by files.
	 *
	 * @param array  $php_files List of files with the Class.
	 * @param string $folder Path of the folder.
	 * @param string $base Namespace base.
	 * @since 2.0
	 */
	private function find_classes( array $php_files, string $folder, string $base ) {
		foreach ( $php_files as $php_file ) {
			$class_name = \substr( $php_file, 0, -4 );
			$path       = $folder . '/' . $php_file;

			if ( \is_file( $path ) ) {
				$this->classes[] = $base . $class_name;

				continue;
			}

			// Verify the Namespace level
			if ( \substr_count( $base . $class_name, '\\' ) < 2 ) {
				continue;
			}

			if ( !\is_dir( $path ) || \strtolower( $php_file ) === $php_file ) {
				continue;
			}

			$sub_php_files = $this->scandir( $folder . '/' . $php_file );
			$this->find_classes( $sub_php_files, $folder . '/' . $php_file, $base . $php_file . '\\' );
		}
	}

}
