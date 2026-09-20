// Ukrainian month names, written out rather than taken from Intl: the page is
// also rendered on the server, where the runtime's locale data is not this
// app's to rely on, and a date that reads differently there than in the
// browser shows up as a hydration mismatch.
const MONTHS = [
    'січня', 'лютого', 'березня', 'квітня', 'травня', 'червня',
    'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня',
];

export const formatPostDate = (date) => {
    if (!date) return '';

    const [year, month, day] = String(date).slice(0, 10).split('-');
    const name = MONTHS[Number(month) - 1];

    return name ? `${Number(day)} ${name} ${year}` : date;
};
