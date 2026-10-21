import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                "background": "#f6f7fb",
                "secondary": "#575e70",
                "inverse-primary": "#bdc7d9",
                "surface-bright": "#f8f9fc",
                "surface-dim": "#d9dadc",
                "surface-container-lowest": "#ffffff",
                "surface-container-low": "#f3f4f7",
                "on-primary-container": "#8690a1",
                "primary-container": "#1f2937",
                "on-surface": "#191c1e",
                "on-secondary-container": "#5c6274",
                "on-background": "#191c1e",
                "on-secondary-fixed": "#141b2b",
                "inverse-on-surface": "#f0f1f3",
                "on-tertiary-container": "#8690a0",
                "secondary-container": "#d9dff5",
                "on-primary": "#ffffff",
                "secondary-fixed": "#dce2f7",
                "on-primary-fixed": "#121c2a",
                "on-secondary": "#ffffff",
                "on-primary-fixed-variant": "#3d4756",
                "outline-variant": "#e6e8ef",
                "secondary-fixed-dim": "#c0c6db",
                "on-error": "#ffffff",
                "surface": "#f8f9fb",
                "tertiary-fixed-dim": "#bdc7d8",
                "surface-container-highest": "#e1e2e4",
                "surface-container": "#edeef0",
                "tertiary": "#0a1520",
                "inverse-surface": "#2e3132",
                "on-tertiary-fixed-variant": "#3e4755",
                "surface-tint": "#555f6f",
                "tertiary-fixed": "#d9e3f4",
                "surface-container-high": "#e7e8ea",
                "error-container": "#ffdad6",
                "surface-variant": "#e1e2e4",
                "on-tertiary": "#ffffff",
                "primary-fixed-dim": "#bdc7d9",
                "on-tertiary-fixed": "#121c28",
                "on-surface-variant": "#44474c",
                "error": "#ba1a1a",
                "on-error-container": "#93000a",
                "tertiary-container": "#1f2936",
                "outline": "#75777c",
                "on-secondary-fixed-variant": "#404758",
                "primary-fixed": "#d9e3f6",
                "primary": "#0a1422"
            },
            borderRadius: {
                "sm": "0.25rem",
                "DEFAULT": "0.375rem",
                "md": "0.5rem",
                "lg": "0.75rem",
                "xl": "1rem",
                "2xl": "1.25rem",
                "3xl": "1.5rem",
                "full": "9999px"
            },
            boxShadow: {
                "soft": "0 1px 2px rgba(16,24,40,0.04), 0 4px 12px rgba(16,24,40,0.05)",
                "card": "0 1px 3px rgba(16,24,40,0.06), 0 10px 28px -10px rgba(16,24,40,0.12)",
                "lift": "0 18px 40px -16px rgba(16,24,40,0.22)"
            },
            spacing: {
                "margin-desktop": "48px",
                "xl": "32px",
                "sm": "8px",
                "md": "16px",
                "xxl": "64px",
                "margin-mobile": "16px",
                "lg": "24px",
                "base": "4px",
                "gutter": "24px",
                "xs": "4px"
            },
            fontFamily: {
                "headline-xl": ["Hanken Grotesk"],
                "body-md": ["Inter"],
                "body-lg": ["Inter"],
                "headline-lg-mobile": ["Hanken Grotesk"],
                "headline-lg": ["Hanken Grotesk"],
                "body-sm": ["Inter"],
                "headline-md": ["Hanken Grotesk"],
                "label-md": ["Inter"],
                "label-caps": ["JetBrains Mono"]
            },
            fontSize: {
                "headline-xl": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                "headline-lg-mobile": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                "headline-md": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                "label-md": ["14px", { "lineHeight": "20px", "fontWeight": "500" }],
                "label-caps": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "500" }],
                "title-lg": ["22px", { "lineHeight": "28px", "fontWeight": "600" }],
                "title-md": ["16px", { "lineHeight": "24px", "letterSpacing": "0.01em", "fontWeight": "600" }],
                "display": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.01em", "fontWeight": "700" }]
            }
        },
    },

    plugins: [forms, require('@tailwindcss/container-queries')],
};
