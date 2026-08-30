import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue } from 'laravel-vue-i18n'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import { definePreset } from '@primevue/themes'
import ToastService from 'primevue/toastservice'

// Casanel's brand accent is amber (see the hero CTA / nav icons), not Aura's
// default emerald — this repoints every PrimeVue component's "primary" color
// (Checkbox, Paginator, Slider, default Button, focus rings, ...) at once.
const CasanelPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{amber.50}',
            100: '{amber.100}',
            200: '{amber.200}',
            300: '{amber.300}',
            400: '{amber.400}',
            500: '{amber.500}',
            600: '{amber.600}',
            700: '{amber.700}',
            800: '{amber.800}',
            900: '{amber.900}',
            950: '{amber.950}',
        },
    },
})

import Toast from 'primevue/toast';
import OverlayBadge from 'primevue/overlaybadge';
import Paginator from 'primevue/paginator';
import Checkbox from 'primevue/checkbox';
import Slider from 'primevue/slider';
import Carousel from 'primevue/carousel';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import RadioButton from 'primevue/radiobutton';
import InputNumber from 'primevue/inputnumber';
import Card from 'primevue/card';
import AutoComplete from 'primevue/autocomplete';
import Message from 'primevue/message';
import InputMask from 'primevue/inputmask';
import Image from 'primevue/image';
import Galleria from 'primevue/galleria';
import Drawer from 'primevue/drawer';

const appName = import.meta.env.VITE_APP_NAME || 'Casanel';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) })
            return app
                .use(plugin)
                .use(i18nVue, {
                    lang: page.props.locale,
                    resolve: lang => {
                        const langs = import.meta.glob('../../lang/*.json', { eager: true })
                        return langs[`../../lang/${lang}.json`].default
                    },
                })
                .use(ZiggyVue, {
                    ...page.props.ziggy,
                    location: new URL(page.props.ziggy.location),
                })
                .use(PrimeVue, {
                    theme: {
                        preset: CasanelPreset,
                        options: {
                            darkModeSelector: 'none'
                        }
                    }
                })
                .use(ToastService)
                .component('Toast', Toast)
                .component('OverlayBadge', OverlayBadge)
                .component('Paginator', Paginator)
                .component('Checkbox', Checkbox)
                .component('Slider', Slider)
                .component('Carousel', Carousel)
                .component('InputText', InputText)
                .component('Button', Button)
                .component('Toolbar', Toolbar)
                .component('IconField', IconField)
                .component('InputIcon', InputIcon)
                .component('Column', Column)
                .component('Tag', Tag)
                .component('Dialog', Dialog)
                .component('Textarea', Textarea)
                .component('Select', Select)
                .component('RadioButton', RadioButton)
                .component('InputNumber', InputNumber)
                .component('Card', Card)
                .component('AutoComplete', AutoComplete)
                .component('Message', Message)
                .component('InputMask', InputMask)
                .component('Image', Image)
                .component('Galleria', Galleria)
        },
    }),
    import.meta.env.VITE_INERTIA_SSR_PORT || 13714
);
