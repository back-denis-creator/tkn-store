import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue } from 'laravel-vue-i18n'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import ToastService from 'primevue/toastservice'

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
                        preset: Aura
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
        },
    }),
    import.meta.env.VITE_INERTIA_SSR_PORT || 13714
);
