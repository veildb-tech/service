import {
  FormControl,
  FormHelperText,
  InputLabel,
  MenuItem,
  Select
} from '@mui/material';
import React, { useCallback } from 'react';
import { useLazyQuery, useQuery } from '@apollo/client';
import { GET_TEMPLATES, GET_TEMPLATE } from 'src/queries';

export function UpdateRuleTemplate(props) {
  const {
    selectedTemplateId,
    setSelectedTemplateId,
    setTemplateName
  } = props;

  const {
    loading: loadingTemplates,
    error: errorTemplates,
    data: templates,
  } = useQuery(GET_TEMPLATES);

  const [loadTemplate] = useLazyQuery(GET_TEMPLATE);

  const changeTemplate = useCallback((event) => {
    setSelectedTemplateId(event.target.value);
    loadTemplate({
      variables: {
        id: event.target.value
      }
    }).then((result) => {
      if (result.data.databaseRuleTemplate.rule) {
        setTemplateName(result.data.databaseRuleTemplate.name);
      }
    });
  }, [loadTemplate, setSelectedTemplateId, setTemplateName]);

  if (!templates || !templates.databaseRuleTemplates.length) return;

  return (
    <div className="flex flex-col">
      <FormControl
        error={!!errorTemplates}
        variant="standard"
        className="select-0"
        sx={{
          minWidth: 350
        }}
      >
        <InputLabel id="template-select-label">Select template</InputLabel>
        <Select
          labelId="template-select-label"
          displayEmpty
          disabled={loadingTemplates || !!errorTemplates}
          onChange={changeTemplate}
          value={selectedTemplateId}
          label="Template"
        >
          {!loadingTemplates
            && !errorTemplates
            && templates.databaseRuleTemplates.map((template) => (
              template.type !== 1 && (
                <MenuItem
                  key={template.id}
                  value={template.id}
                >
                  <span>{template.name}</span>
                </MenuItem>
              )
            ))}
        </Select>
        <FormHelperText>{errorTemplates ? errorTemplates.message : ''}</FormHelperText>
      </FormControl>
    </div>
  );
}

UpdateRuleTemplate.propTypes = {};
