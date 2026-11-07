<?php
/**
 * Elementor compatibility widget backed by the Rank Math Free renderer.
 */

namespace TFSC\RankMathBreadcrumbs;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

class Widget_Breadcrumbs extends Widget_Base {

    public function get_name() {
        return 'breadcrumbs';
    }

    public function get_title() {
        return esc_html__( 'TFSC Breadcrumbs', 'tfsc-rank-math-breadcrumbs' );
    }

    public function get_icon() {
        return 'eicon-product-breadcrumbs';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_keywords() {
        return [ 'breadcrumbs', 'rank math', 'broodkruimels', 'tfsc' ];
    }

    protected function is_dynamic_content(): bool {
        // One shared template must produce a different trail for each URL.
        return true;
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_breadcrumbs_content',
            [ 'label' => esc_html__( 'Breadcrumbs', 'tfsc-rank-math-breadcrumbs' ) ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__( 'Alignment', 'tfsc-rank-math-breadcrumbs' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [ 'title' => esc_html__( 'Left', 'tfsc-rank-math-breadcrumbs' ), 'icon' => 'eicon-text-align-left' ],
                    'center' => [ 'title' => esc_html__( 'Center', 'tfsc-rank-math-breadcrumbs' ), 'icon' => 'eicon-text-align-center' ],
                    'right' => [ 'title' => esc_html__( 'Right', 'tfsc-rank-math-breadcrumbs' ), 'icon' => 'eicon-text-align-right' ],
                ],
                'prefix_class' => 'elementor%s-align-',
            ]
        );

        $this->add_control(
            'html_tag',
            [
                'label' => esc_html__( 'HTML tag', 'tfsc-rank-math-breadcrumbs' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [ '' => esc_html__( 'Default', 'tfsc-rank-math-breadcrumbs' ), 'p' => 'p', 'div' => 'div', 'nav' => 'nav', 'span' => 'span' ],
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Breadcrumbs', 'tfsc-rank-math-breadcrumbs' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Match the saved Pro control names and wrapper selectors. Elementor
        // continues to resolve the existing global colors and advanced controls.
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}}',
                'global' => [ 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ],
            ]
        );
        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Text color', 'tfsc-rank-math-breadcrumbs' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}}' => 'color: {{VALUE}};' ],
            ]
        );
        $this->add_control(
            'link_color',
            [
                'label' => esc_html__( 'Link color', 'tfsc-rank-math-breadcrumbs' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} a' => 'color: {{VALUE}};' ],
            ]
        );
        $this->add_control(
            'link_hover_color',
            [
                'label' => esc_html__( 'Link hover color', 'tfsc-rank-math-breadcrumbs' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} a:hover' => 'color: {{VALUE}};' ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
            // The Free engine applies the existing TFSC item filters and emits
            // the same nav.rank-math-breadcrumb markup as the current widgets.
            $args = [];
            $tag = $this->get_settings( 'html_tag' );
            if ( in_array( $tag, [ 'p', 'div', 'nav', 'span' ], true ) ) {
                $args = [ 'wrap_before' => '<' . $tag . '>', 'wrap_after' => '</' . $tag . '>' ];
            }
            rank_math_the_breadcrumbs( $args );
        }
    }
}
