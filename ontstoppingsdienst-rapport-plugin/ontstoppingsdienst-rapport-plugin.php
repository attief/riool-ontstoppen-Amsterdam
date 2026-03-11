<?php
/**
 * Plugin Name: Ontstoppingsdienst 24-7 Rapport
 * Description: Voegt een shortcode toe voor het technische automatiseringsrapport van Ontstoppingsdienst24-7.nl.
 * Version: 1.0.0
 * Author: Codex
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (! defined('ABSPATH')) {
    exit;
}

final class OD247_Automation_Report_Plugin
{
    private static ?OD247_Automation_Report_Plugin $instance = null;

    public static function instance(): OD247_Automation_Report_Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('init', [$this, 'register_shortcode']);
    }

    public function register_shortcode(): void
    {
        add_shortcode('ontstoppingsdienst_rapport', [$this, 'render_report_shortcode']);
    }

    public function render_report_shortcode(): string
    {
        wp_enqueue_style('od247-report-google-fonts', 'https://fonts.googleapis.com/css2?family=Canela:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap', [], null);
        wp_enqueue_script('od247-report-tailwind', 'https://cdn.tailwindcss.com', [], null, false);
        wp_enqueue_script('od247-report-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js', [], null, true);

        wp_register_style('od247-report-style', false);
        wp_enqueue_style('od247-report-style');
        wp_add_inline_style('od247-report-style', $this->get_inline_styles());

        wp_register_script('od247-report-script', false, [], null, true);
        wp_enqueue_script('od247-report-script');
        wp_add_inline_script('od247-report-script', $this->get_inline_script());

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/report-content.php';
        return (string) ob_get_clean();
    }

    private function get_inline_styles(): string
    {
        return <<<'CSS'
:root {
    --primary: #1e5aa8;
    --accent: #ff6b35;
    --neutral: #4a5568;
    --success: #38a169;
    --warning: #e53e3e;
    --background: #ffffff;
    --surface: #fafafa;
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
}

.od247-report-wrapper {
    font-family: 'Inter', sans-serif;
    line-height: 1.7;
    color: var(--text-primary);
}

@media (max-width: 767px) {
    .od247-report-wrapper {
        overflow-x: hidden;
    }
}

.od247-report-wrapper .font-display {
    font-family: 'Canela', serif;
}

.od247-report-wrapper .hero-gradient {
    background: linear-gradient(135deg, rgba(30, 90, 168, 0.95) 0%, rgba(30, 90, 168, 0.85) 50%, rgba(255, 107, 53, 0.8) 100%);
}

.od247-report-wrapper .bento-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.od247-report-wrapper .bento-main {
    grid-column: span 2;
    min-height: 400px;
}

.od247-report-wrapper .toc-fixed {
    position: fixed;
    top: 2rem;
    left: 2rem;
    width: 280px;
    height: calc(100vh - 4rem);
    overflow-y: auto;
    z-index: 40;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(30, 90, 168, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.od247-report-wrapper .content-main {
    margin-left: 320px;
    padding: 2rem;
}

.od247-report-wrapper .citation-link {
    color: var(--primary);
    text-decoration: underline;
    cursor: pointer;
}

.od247-report-wrapper .citation-link:hover {
    color: var(--accent);
}

.od247-report-wrapper img {
    max-width: 100%;
    height: auto;
}

@media (max-width: 1280px) {
    .od247-report-wrapper .toc-fixed {
        position: static;
        width: 100%;
        height: auto;
        margin-bottom: 2rem;
    }

    .od247-report-wrapper .content-main {
        margin-left: 0;
    }
}

@media (max-width: 768px) {
    .od247-report-wrapper .bento-main {
        grid-column: span 1;
    }

    .od247-report-wrapper .bento-grid {
        grid-template-columns: 1fr;
    }

    .od247-report-wrapper .content-main,
    .od247-report-wrapper .toc-fixed,
    .od247-report-wrapper .hero .px-8,
    .od247-report-wrapper section {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }

    .od247-report-wrapper .toc-fixed {
        margin-left: 0;
        margin-right: 0;
        width: calc(100% - 2rem);
    }
}
CSS;
    }

    private function get_inline_script(): string
    {
        return <<<'JS'
document.querySelectorAll('.od247-report-wrapper a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

const sections = document.querySelectorAll('.od247-report-wrapper section[id]');
const tocLinks = document.querySelectorAll('.od247-report-wrapper .toc-fixed a');

function highlightActiveSection() {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        if (window.pageYOffset >= sectionTop - 200) {
            current = section.getAttribute('id');
        }
    });

    tocLinks.forEach(link => {
        link.classList.remove('bg-blue-100', 'text-blue-700', 'font-semibold');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('bg-blue-100', 'text-blue-700', 'font-semibold');
        }
    });
}

window.addEventListener('scroll', highlightActiveSection);
highlightActiveSection();
JS;
    }
}

OD247_Automation_Report_Plugin::instance();
