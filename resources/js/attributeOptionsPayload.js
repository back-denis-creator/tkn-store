// The option editor keeps whole objects in the form: `meta` holds the chosen
// colour group together with every one of its subcategories, `sub_meta` holds
// the chosen subcategory, and `src`, `new_preview` and `_pendingDelete` only
// feed the thumbnail and the row state.
//
// Inertia turns every leaf of that into its own POST variable. The "Тканина"
// attribute (83 options) therefore sent 1006 variables, and PHP accepts 1000 by
// default. PHP drops the rest and reports nothing: the last options kept their
// old values, and `deleted_option_ids` never arrived at all, so a deletion was
// lost too. The admin saw a normal "saved" screen.
//
// The server reads ids, not objects. Sending the ids brings the same attribute
// down to 670 variables.
export const attributeOptionsPayload = (data) => ({
    ...data,
    options: (data.options ?? []).map((option) => ({
        id: option.id,
        value: option.value,
        new_file: option.new_file,
        // The "Однотонні" group has id 0, so this must test for null, not for
        // a falsy value.
        meta: option.meta?.id ?? null,
        sub_meta: option.sub_meta?.id ?? null,
        description: option.description,
        article: option.article,
        default_color_ids: option.default_color_ids,
    })),
    // Last on purpose. PHP cuts a too-long request from the end, so a missing
    // flag is the only sign the server gets that the request arrived cut short.
    payload_complete: 1,
});
