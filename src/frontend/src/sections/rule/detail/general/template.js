import PropTypes from 'prop-types';
import {
  FormControl, FormHelperText, InputLabel, MenuItem, Select
} from '@mui/material';
import { useRulesDispatch } from 'src/contexts/rule/rule-context';
import React, { useCallback } from 'react';
import { useLazyQuery, useQuery } from '@apollo/client';
import { SeverityPill } from 'src/components/severity-pill';
import { GET_TEMPLATES, GET_TEMPLATE } from 'src/queries';
import { RuleSuggestion } from './suggestion';

export function RuleTemplate(props) {
  const {
    selectedDb, selectedTemplate, setSelectedTemplate
  } = props;
  const dispatch = useRulesDispatch();

  const {
    loading: loadingTemplates,
    error: errorTemplates,
    data: templates,
  } = useQuery(GET_TEMPLATES);

  const [loadTemplate] = useLazyQuery(GET_TEMPLATE);

  const changeTemplate = useCallback((event) => {
    let confirmed = true;
    if (selectedTemplate !== event.target.value) {
      // eslint-disable-next-line no-restricted-globals,no-alert
      confirmed = confirm('This action will override all your changes. Are you sure?');
    }

    if (confirmed) {
      setSelectedTemplate(event.target.value);
      loadTemplate({ variables: { id: event.target.value } }).then((result) => {
        if (result.data.databaseRuleTemplate.rule) {
          dispatch({
            type: 'update',
            rules: result.data.databaseRuleTemplate.rule,
          });
        }
      });
    }
  }, [dispatch, loadTemplate, selectedTemplate, setSelectedTemplate]);
  const isRecommended = (template) => selectedDb && selectedDb.platform && selectedDb.platform === template.platform;

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
          value={selectedTemplate}
          label="Template"
        >
          {!loadingTemplates
            && !errorTemplates
            && templates.databaseRuleTemplates.map((template) => (
              <MenuItem
                key={template.id}
                value={template.id}
              >
                <span>{template.name}</span>
                {isRecommended(template) && (
                  <SeverityPill className="ml-3" color="success">Recommended</SeverityPill>
                )}
                {template.type === 1 && (
                  <SeverityPill className="ml-3" color="info">System</SeverityPill>
                )}
              </MenuItem>
            ))}
        </Select>
        <FormHelperText>{errorTemplates ? errorTemplates.message : ''}</FormHelperText>
      </FormControl>
      <RuleSuggestion selectedDb={selectedDb} />
    </div>
  );
}

RuleTemplate.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  selectedDb: PropTypes.object,
};
