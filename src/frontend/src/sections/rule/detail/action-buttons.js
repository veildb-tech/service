import React, { useState } from 'react';
import PropTypes from 'prop-types';
import { Button } from '@mui/material';
import { useMutation } from '@apollo/client';
import { useRules } from 'src/contexts/rule/rule-context';
import { useRuleAdditions } from 'src/contexts/rule/rule-additions-context';
import { DELETE_RULE } from 'src/queries';
import { DeleteDialog } from 'src/components/delete-dialog';
import { useRouter } from 'next/router';
import { useUrl } from 'src/hooks/use-url';
import SaveUpdateTemplatePopup from '../save-update-template-popup';

export function ActionButtons(props) {
  const {
    onSaveButtonClick,
    ruleName,
    ruleId,
    handleError,
    valid,
    database,
    onSaveTemplateSuccess,
    setSelectedTemplate
  } = props;

  const rules = useRules();
  const additions = useRuleAdditions();
  const [open, setOpen] = useState(false);
  const [deleteRuleMutation, { error: deleteError }] = useMutation(DELETE_RULE);
  const router = useRouter();
  const url = useUrl();

  if (deleteError) {
    handleError(deleteError.message);
  }

  const handleClickOpen = () => {
    setOpen(true);
  };

  const deleteRule = () => {
    deleteRuleMutation({ variables: { id: ruleId } }).then(() => {
      router.push(url.getUrl('rules'));
    });
  };

  return (
    <div className="flex items-center gap-4">
      {ruleId && (
      <DeleteDialog
        message={
              'Are you sure you want to remove this rule.'
          + 'It will not remove database however all configurations will be lost'
            }
        onConfirm={deleteRule}
      />
      )}

      <Button
        className="button-6"
        onClick={handleClickOpen}
        disabled={!ruleName || !rules.length}
      >
        Save as template
      </Button>

      <SaveUpdateTemplatePopup
        open={open}
        setOpen={setOpen}
        onSaveTemplateSuccess={onSaveTemplateSuccess}
        handleError={handleError}
        database={database}
        setSelectedTemplate={setSelectedTemplate}
      />

      <Button
        className={`
        ${!valid && '!bg-dbm-color-white !border !border-solid !border-dbm-color-secondary'}
        button-7 min-w-[90px]
        `}
        disabled={!valid}
        onClick={() => onSaveButtonClick(rules, additions)}
      >
        Save
      </Button>
    </div>
  );
}

ActionButtons.propTypes = {
  onSaveButtonClick: PropTypes.func,
  ruleName: PropTypes.string,
  ruleId: PropTypes.string,
  handleError: PropTypes.func,
  valid: PropTypes.bool
};
