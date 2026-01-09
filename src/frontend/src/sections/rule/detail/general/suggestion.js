import PropTypes from 'prop-types';
import {
  Alert,
  Typography,
  Link
} from '@mui/material';
import React, { useState } from 'react';
import { useLazyQuery } from '@apollo/client';
import { GET_SUGGESTED_RULE } from 'src/queries';
import { useRulesDispatch } from 'src/contexts/rule/rule-context';

export function RuleSuggestion(props) {
  const {
    selectedDb,
    asAlert
  } = props;
  const suggestedRule = selectedDb?.databaseRuleSuggestions
    && selectedDb?.databaseRuleSuggestions.length
    ? selectedDb?.databaseRuleSuggestions[0].id : false;
  const [success, setSuccess] = useState(false);

  const dispatch = useRulesDispatch();
  const [loadSuggestedRule] = useLazyQuery(GET_SUGGESTED_RULE);

  const applyAutoSuggestion = () => {
    loadSuggestedRule({
      variables: {
        id: suggestedRule
      }
    }).then((result) => {
      dispatch({
        type: 'update',
        rules: result.data.databaseRuleSuggestion.rule,
      });
      setSuccess(true);
    });
  };

  if (!suggestedRule) {
    return '';
  }

  if (asAlert) {
    return (
      <>
        { success && (
          <Alert className="mb-4">
            Rule has been applied successfully
          </Alert>
        )}

        { !success && (
          <Alert className="mb-4">
            There are rule suggestions for database schema. Please note that this functionality is still in beta
            &nbsp;
            <Typography variant="heading" color="info.dark">
              {/* eslint-disable-next-line jsx-a11y/anchor-is-valid */}
              <Link
                onClick={applyAutoSuggestion}
                variant="inherit"
                underline="none"
                component="button"
                sx={{ marginTop: '-2px' }}
              >
                apply
              </Link>
            </Typography>
          </Alert>
        )}
      </>
    );
  }
  return (
    <div>
      <Typography className="sub-heading-0" variant="heading1">
        or apply auto-suggested rules
        &nbsp;
        { !success && (

          <Typography variant="caption" color="info.dark">
            {/* eslint-disable-next-line jsx-a11y/anchor-is-valid */}
            <Link
              onClick={applyAutoSuggestion}
              variant="inherit"
              underline="none"
              component="button"
              sx={{ marginTop: '-2px' }}
            >
              apply
            </Link>
          </Typography>
        )}
        { success && (<Typography variant="caption" color="success.main">applied!</Typography>)}
      </Typography>
    </div>
  );
}

RuleSuggestion.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  selectedDb: PropTypes.object,
  asAlert: PropTypes.bool
};
