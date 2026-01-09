import React from 'react';
import CheckRoundedIcon from '@mui/icons-material/CheckRounded';
import ErrorOutlineRoundedIcon from '@mui/icons-material/ErrorOutlineRounded';
import NoteAltIcon from '@mui/icons-material/NoteAlt';
import merge from 'deepmerge';

export default {
  legendItems: {
    APPLIED: {
      title: 'applied',
      icon: (additionalProps) => {
        const defaultProps = { sx: { color: '#10B981' } };
        const props = merge(defaultProps, additionalProps);

        return <CheckRoundedIcon {...props} />;
      },
    },

    ACTION_REQUIRED: {
      title: 'action required',
      icon: (additionalProps) => {
        const defaultProps = { sx: { color: '#E64444' } };
        const props = merge(defaultProps, additionalProps);

        return <ErrorOutlineRoundedIcon {...props} />;
      },
    },

    SUGGESTED_CONDITIONS: {
      title: 'suggested conditions',
      icon: (additionalProps) => {
        const defaultProps = { sx: { color: '#E1A852' } };
        const props = merge(defaultProps, additionalProps);

        return <NoteAltIcon {...props} />;
      },
    }
  }
};
