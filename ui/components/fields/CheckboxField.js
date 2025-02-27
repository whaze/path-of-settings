import { CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const CheckboxField = ({ id, label, description, value, onChange, error }) => {
    return (
        <div className="pos-field pos-field-checkbox">
            <CheckboxControl
                label={label}
                help={error || description}
                checked={!!value}
                onChange={onChange}
                className={error ? 'has-error' : ''}
            />
        </div>
    );
};

export default CheckboxField;