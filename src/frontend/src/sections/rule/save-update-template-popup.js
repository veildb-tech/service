import React, { useState } from 'react';
import PropTypes from 'prop-types';
import {
  Button,
  Dialog,
  Divider,
  FormControl,
  InputLabel,
  MenuItem,
  Select,
  TextField,
  Typography
} from '@mui/material';
import {
  CREATE_RULE_TEMPLATE,
  UPDATE_RULE_TEMPLATE
} from 'src/queries';
import CloseIcon from '@mui/icons-material/Close';
import { useMutation } from '@apollo/client';
import { useRules } from 'src/contexts/rule/rule-context';
import { UpdateRuleTemplate } from './detail/general/update-rule-template';

function SaveUpdateTemplatePopup(props) {
  const {
    open,
    setOpen,
    onSaveTemplateSuccess,
    handleError,
    database,
    setSelectedTemplate
  } = props;

  const rules = useRules();
  const [templateName, setTemplateName] = useState('');
  const [selectedTemplateId, setSelectedTemplateId] = useState('');
  const [saveAsNew, setSaveAsNew] = useState(1);
  const [updateDatabaseRuleTemplate] = useMutation(UPDATE_RULE_TEMPLATE);
  const [saveRuleTemplate, { error: saveError }] = useMutation(CREATE_RULE_TEMPLATE);

  if (saveError) {
    handleError(saveError.message);
  }

  const saveAsTemplate = () => {
    if (selectedTemplateId) {
      updateDatabaseRuleTemplate({
        variables: {
          id: selectedTemplateId,
          rule: rules,
          name: templateName
        },
      }).then(() => {
        setOpen(false);
      });
    } else {
      saveRuleTemplate({
        variables: {
          rule: rules,
          name: templateName
        },
      }).then(() => {
        onSaveTemplateSuccess(true);
        setOpen(false);
      });
    }
  };

  const handleClose = () => {
    setOpen(false);
  };

  const changeOptionsType = (event) => {
    setSelectedTemplateId('');
    setTemplateName('');
    setSaveAsNew(event.target.value);
  };

  return (
    <Dialog
      className="dialog-0"
      open={open}
      onClose={handleClose}
      aria-labelledby="alert-dialog-title"
      aria-describedby="alert-dialog-description"
    >

      <div className="dialog-0-content mb-6 w-[752px]">
        <div className="flex justify-between">
          <Typography variant="h4">Save as template</Typography>

          <Button
            onClick={handleClose}
            className="button-3"
          >
            <CloseIcon />
          </Button>
        </div>
      </div>

      <div className="dialog-0-content grid grid-cols-2 gap-3 min-h-[76px]">
        <FormControl
          variant="standard"
          className="select-0 w-full h-[51px]"
          sx={{
            minWidth: 250
          }}
        >
          <InputLabel id="template-select-label">Options type</InputLabel>
          <Select
            value={saveAsNew}
            onChange={changeOptionsType}
          >
            <MenuItem value={1}> Save New Template </MenuItem>
            <MenuItem value={0}> Update an existing template </MenuItem>
          </Select>
        </FormControl>

        {
          saveAsNew ? (
            <TextField
              type="text"
              className="input-0 w-full !min-w-[350px]"
              onChange={(event) => setTemplateName(event.target.value)}
              name="template-name"
              value={templateName}
              label="Name"
              placeholder="Template name"
            />
          ) : (
            <UpdateRuleTemplate
              setTemplateName={setTemplateName}
              selectedTemplateId={selectedTemplateId}
              setSelectedTemplateId={setSelectedTemplateId}
              setSelectedTemplate={setSelectedTemplate}
              selectedDb={database}
            />
          )
        }
      </div>

      <Divider className="!my-6" />

      <div className="dialog-0-content flex items-center gap-3">
        <Button
          className="button-4 w-full"
          onClick={handleClose}
        >
          Close
        </Button>

        <Button
          className="button-0 w-full"
          onClick={saveAsTemplate}
        >
          Save
        </Button>
      </div>
    </Dialog>
  );
}

export default SaveUpdateTemplatePopup;

SaveUpdateTemplatePopup.propTypes = {
  onSaveTemplateSuccess: PropTypes.func
};
