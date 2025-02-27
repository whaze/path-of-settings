import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const TextField = ({ id, label, description, placeholder, value, onChange, error }) => {
    return (
        <div className="pos-field pos-field-text">
            <TextControl
                label={label}
                help={error || description}
                value={value || ''}
                onChange={onChange}
                placeholder={placeholder}
                className={error ? 'has-error' : ''}
            />
        </div>
    );
};

export default TextField;