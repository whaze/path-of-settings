import { createRoot } from '@wordpress/element';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {
    Button,
    Panel,
    PanelBody,
    PanelRow,
    Spinner,
    Notice
} from '@wordpress/components';

// Import field components
import TextField from './components/fields/TextField';
import SelectField from './components/fields/SelectField';
import CheckboxField from './components/fields/CheckboxField';
import TextareaField from './components/fields/TextareaField';

// Field component map
const fieldComponents = {
    text: TextField,
    select: SelectField,
    checkbox: CheckboxField,
    textarea: TextareaField,
};

// Main app component
const App = () => {
    const [settings, setSettings] = useState({});
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [notice, setNotice] = useState(null);
    const [errors, setErrors] = useState({});
    
    // Get the current page from global data
    const currentPage = window.posData?.currentPage || null;
    
    // Load settings on mount
    useEffect(() => {
        if (!currentPage) {
            setLoading(false);
            return;
        }
        
        loadSettings();
    }, [currentPage]);
    
    // Load settings from API
    const loadSettings = async () => {
        if (!currentPage) return;
        
        setLoading(true);
        
        try {
            const response = await apiFetch({
                path: `/pos/v1/settings/${currentPage.id}`,
                method: 'GET',
            });
            
            setSettings(response);
            setErrors({});
            setNotice(null);
        } catch (error) {
            console.error('Failed to load settings:', error);
            setNotice({
                status: 'error',
                message: __('Failed to load settings.', 'path-of-settings'),
            });
        } finally {
            setLoading(false);
        }
    };
    
    // Save settings to API
    const saveSettings = async () => {
        if (!currentPage) return;
        
        setSaving(true);
        
        try {
            const response = await apiFetch({
                path: `/pos/v1/settings/${currentPage.id}`,
                method: 'POST',
                data: settings,
            });
            
            setSettings(response);
            setErrors({});
            setNotice({
                status: 'success',
                message: __('Settings saved successfully.', 'path-of-settings'),
            });
        } catch (error) {
            console.error('Failed to save settings:', error);
            
            if (error.data?.errors) {
                setErrors(error.data.errors);
            }
            
            setNotice({
                status: 'error',
                message: __('Failed to save settings.', 'path-of-settings'),
            });
        } finally {
            setSaving(false);
        }
    };
    
    // Handle field change
    const handleFieldChange = (fieldId, value) => {
        setSettings({
            ...settings,
            [fieldId]: value,
        });
    };
    
    // If no current page, show error
    if (!currentPage) {
        return (
            <Notice status="error" isDismissible={false}>
                {__('No page configuration found.', 'path-of-settings')}
            </Notice>
        );
    }
    
    // If loading, show spinner
    if (loading) {
        return (
            <div className="pos-loading">
                <Spinner />
                <p>{__('Loading settings...', 'path-of-settings')}</p>
            </div>
        );
    }
    
    // Render fields
    const renderFields = (fields) => {
        return fields.map(field => {
            const FieldComponent = fieldComponents[field.type];
            
            if (!FieldComponent) {
                return (
                    <PanelRow key={field.id}>
                        <Notice status="error" isDismissible={false}>
                            {__(`Unknown field type: ${field.type}`, 'path-of-settings')}
                        </Notice>
                    </PanelRow>
                );
            }
            
            const value = settings[field.id] !== undefined 
                ? settings[field.id] 
                : field.config.default;
            
            return (
                <PanelRow key={field.id}>
                    <FieldComponent
                        id={field.id}
                        value={value}
                        onChange={(value) => handleFieldChange(field.id, value)}
                        error={errors[field.id]}
                        {...field.config}
                    />
                </PanelRow>
            );
        });
    };
    
    return (
        <div className="pos-settings-page">
            <h1>{currentPage.title}</h1>
            
            {notice && (
                <Notice
                    status={notice.status}
                    onRemove={() => setNotice(null)}
                >
                    {notice.message}
                </Notice>
            )}
            
            <Panel>
                <PanelBody
                    title={__('Settings', 'path-of-settings')}
                    initialOpen={true}
                >
                    {renderFields(currentPage.fields)}
                </PanelBody>
            </Panel>
            
            <div className="pos-actions">
                <Button
                    isPrimary
                    onClick={saveSettings}
                    isBusy={saving}
                    disabled={saving}
                >
                    {saving
                        ? __('Saving...', 'path-of-settings')
                        : __('Save Settings', 'path-of-settings')
                    }
                </Button>
            </div>
        </div>
    );
};

// Initialize the app
document.addEventListener('DOMContentLoaded', () => {
    const appContainer = document.getElementById('pos-app');
    
    if (appContainer) {
        const root = createRoot(appContainer);
        root.render(<App />);
    }
});