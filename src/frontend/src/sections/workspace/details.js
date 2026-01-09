import PropTypes from 'prop-types';
import { useState } from 'react';
import {
  Alert,
  CircularProgress,
  Button,
  TextField,
  Typography,
  Divider
} from '@mui/material';
import { useMutation } from '@apollo/client';
import { UPDATE_WORKSPACE } from 'src/queries';
import * as React from 'react';
import { useAuth } from 'src/hooks/use-auth';
import { useRouter } from 'next/router';
import { DeleteWorkspace } from './details/delete-workspace';

export function WorkspaceDetails(props) {
  const { data } = props;
  const [name, setName] = useState(data.name);

  const [saveWorkspace, { data: savedData, loading: saving, error: saveError }] = useMutation(UPDATE_WORKSPACE);

  const handleSubmit = (event) => {
    event.preventDefault();

    saveWorkspace({
      variables: {
        code: data.code,
        name
      }
    }).then(() => {});
  };

  const auth = useAuth();
  const router = useRouter();

  const onDelete = () => {
    auth.signOut();
    router.push('/auth/login');
  };

  return (
    <div className="card w-full">
      <div className="card-content flex gap-1 mb-7">
        <Typography variant="h4">Workspace Configurations</Typography>
      </div>

      {savedData && <Alert severity="success">Workspace Saved successfully</Alert>}
      {saveError && <Alert severity="error">{saveError.message}</Alert>}

      <form
        autoComplete="off"
        noValidate
        onSubmit={handleSubmit}
      >
        <div className="card-content flex gap-3">
          <TextField
            className="input-0 w-full"
            placeholder="Type Name"
            label="Name"
            name="name"
            required
            onChange={(event) => setName(event.target.value)}
            value={name}
          />

          <TextField
            className="input-0 w-full"
            placeholder="Type Code"
            label="Code"
            name="code"
            disabled
            required
            value={data.code}
          />
        </div>

        <Divider className="!my-6" />

        <div className="card-content flex justify-between">
          {saving && <CircularProgress />}

          <Button
            className="button-0 !min-w-[170px]"
            type="submit"
            disabled={saving}
            title="Save details"
          >
            Save details
          </Button>

          <DeleteWorkspace
            code={data.code}
            id={data.id}
            onDelete={onDelete}
          />
        </div>
      </form>
    </div>
  );
}

WorkspaceDetails.propTypes = {
  // eslint-disable-next-line react/forbid-prop-types
  data: PropTypes.object
};
