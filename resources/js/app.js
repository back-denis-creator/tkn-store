import './bootstrap';
import '../css/app.css';
import 'primeicons/primeicons.css'

import { createSSRApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import ToastService from 'primevue/toastservice'

import Editor from 'primevue/editor';
Editor.methods.renderValue = function renderValue(value) {
    if (this.quill) {
      if (value) {
        const delta = this.quill.clipboard.convert({ html: value });
        this.quill.setContents(delta, 'silent');
      } else {
        this.quill.setText('');
      }
    }
};
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import FileUpload from 'primevue/fileupload';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Column from 'primevue/column';
import Rating from 'primevue/rating';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import RadioButton from 'primevue/radiobutton';
import InputNumber from 'primevue/inputnumber';
import Card from 'primevue/card';
import AutoComplete from 'primevue/autocomplete';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura
                }
            })
            .use(ToastService)
            .component('Editor', Editor)
            .component('InputText', InputText)
            .component('Button', Button)
            .component('Toolbar', Toolbar)
            .component('FileUpload', FileUpload)
            .component('DataTable', DataTable)
            .component('IconField', IconField)
            .component('InputIcon', InputIcon)
            .component('Column', Column)
            .component('Rating', Rating)
            .component('Tag', Tag)
            .component('Dialog', Dialog)
            .component('Textarea', Textarea)
            .component('Select', Select)
            .component('RadioButton', RadioButton)
            .component('InputNumber', InputNumber)
            .component('Card', Card)
            .component('AutoComplete', AutoComplete)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
