import React from 'react';
import { Button, Typography } from '@mui/material';
import SyntaxHighlighter from 'react-syntax-highlighter';
import docco from 'react-syntax-highlighter/dist/cjs/styles/hljs/docco';
import { TabContext, TabPanel } from '@mui/lab';
import NextLink from 'next/link';
import { usePermission } from 'src/hooks/use-permission';
import Switch1 from 'src/elements/switch1';
import { DocumentIcon } from 'src/elements/icons';
import { QuickGuide } from './quick-guide';

export function IndexInstallation(props) {
  const { isNewWorkspace } = props;
  const [value, setValue] = React.useState('debian');
  const [clientValue, setClientValue] = React.useState('ubuntu');
  const [copyStatus, setToggleCopyStatus] = React.useState('');
  const [copyStatusMessage, setCopyStatusMessage] = React.useState('');
  const domain = process.env.NEXT_PUBLIC_DOWNLOAD_URL ?? 'https://app.dbvisor.pro/';

  const handleChange = (newValue) => {
    setValue(newValue);
  };
  const { isAdmin } = usePermission();

  const copyCode = (text, value) => {
    navigator.clipboard.writeText(text).then(
      () => {
        setCopyStatusMessage('Copied');
        setToggleCopyStatus(value);
      }
    )
      .catch(
        () => {
          setCopyStatusMessage('Error');
          setToggleCopyStatus(value);
        }
      );

    setTimeout(() => {
      setCopyStatusMessage('');
      setToggleCopyStatus('');
    }, 5000);
  };

  const agentSwitchOptions = [
    { title: 'debian / fedora', value: 'debian' },
    { title: 'alpine / centos', value: 'alpine' }
  ];

  const clientSwitchOptions = [
    { title: 'Ubuntu', value: 'ubuntu' },
    { title: 'Mac (Apple)', value: 'mac_arm' },
    { title: 'Mac (Intel)', value: 'mac_amd' },
    { title: 'Windows', value: 'windows' }
  ];

  const agentInstallations = [
    {
      value: 'debian',
      installations: `curl ${domain}download/dbvisor-agent-install | bash 
source ~/.bashrc`
    },
    {
      value: 'alpine',
      installations: `curl ${domain}download/dbvisor-agent-install | sh`
    }
  ];

  const clientInstallations = [
    {
      value: 'ubuntu',
      downloadLink: `${domain}download/dbvisor_linux.zip`,
      // eslint-disable-next-line max-len
      installations: `curl ${domain}download/dbvisor_linux.zip -o /tmp/dbvisor_linux.zip && sudo unzip -o /tmp/dbvisor_linux.zip -d /usr/local/dbvisor/ && rm /tmp/dbvisor_linux.zip 
`
      + 'sudo ln -s /usr/local/dbvisor/bin/dbvisor_linux /usr/local/bin/dbvisor\n'
      + 'source $HOME/.bashrc'
    },
    {
      value: 'mac_arm',
      downloadLink: `${domain}download/dbvisor_mac_arm64.zip`,
      // eslint-disable-next-line max-len
      installations: `curl ${domain}download/dbvisor_mac_arm64.zip -o /tmp/dbvisor_arm64.zip && sudo unzip -o /tmp/dbvisor_arm64.zip -d /usr/local/dbvisor/ && rm /tmp/dbvisor_arm64.zip 
`
        + 'sudo ln -s /usr/local/dbvisor/bin/dbvisor_mac_arm64 /usr/local/bin/dbvisor\n'
        + 'source $HOME/.zshrc'
    },
    {
      value: 'mac_amd',
      downloadLink: `${domain}download/dbvisor_mac_amd64.zip`,
      // eslint-disable-next-line max-len
      installations: `curl ${domain}download/dbvisor_mac_amd64.zip -o /tmp/dbvisor_mac_amd64.zip && sudo unzip -o /tmp/dbvisor_mac_amd64.zip -d /usr/local/dbvisor/ && rm /tmp/dbvisor_mac_amd64.zip 
`
        + 'sudo ln -s /usr/local/dbvisor/bin/dbvisor_mac_amd64 /usr/local/bin/dbvisor\n'
        + 'source $HOME/.zshrc'
    },
    {
      value: 'windows',
      installations: 'comming soon...'
    }
  ];

  const tabPanelTemplate = (tab) => (
    <TabPanel
      key={tab.value}
      value={tab.value}
      className="!p-[5px] relative"
    >
      <div className="bg-dbm-color-primary-dark rounded-r-[8px] rounded-bl-[8px] ">
        <SyntaxHighlighter
          language="bash"
          style={docco}
          className="!bg-inherit !text-dbm-color-white max-w-[94%]"
        >
          {tab.installations}
        </SyntaxHighlighter>

        {
          copyStatus === tab.value ? (
            <div className="absolute top-[13px] right-[20px] text-dbm-color-secondary-dark">
              {copyStatusMessage}
            </div>
          ) : (
            <Button
              className="button-11"
              onClick={() => copyCode(tab.installations, tab.value)}
            >
              <DocumentIcon />
            </Button>
          )
        }
      </div>
      { tab.downloadLink && (
        <Typography variant="overline">
          ...or download by&nbsp;
          <NextLink className="link-0" href={tab.downloadLink}>link</NextLink>
        </Typography>
      )}
    </TabPanel>
  );

  return (
    <div className="flex">
      {
        isNewWorkspace && (
          <QuickGuide />
        )
      }
      <div className="flex flex-col grow max-w-[750px]">
        {isAdmin() && (
          <div className="mb-14">
            <Typography
              variant="h4"
              className="!mb-2"
            >
              Agent Installation
            </Typography>
            <Typography
              variant="overline"
              className="block items-center"
            >
              <span>
                Please, ensure all requirements are installed. To get more information visit our
              </span>

              <span className="inline-block">
                <NextLink
                  href="https://dbvisor.gitbook.io/"
                  className="link-0 link-1 normal-case ml-3 with-document-icon"
                  target="_blank"
                >
                  <DocumentIcon />
                  Documentation
                </NextLink>
              </span>
            </Typography>
            <Switch1
              className="self-start w-[250px] mt-3"
              selectedOption={value}
              setOption={handleChange}
              options={agentSwitchOptions}
              name="searchtype"
            />
            <TabContext value={value}>
              {agentInstallations.map((tab) => tabPanelTemplate(tab))}
            </TabContext>
          </div>
        )}
        <div>
          <Typography variant="h4" className="!mb-2">Client Installation</Typography>
          <Typography
            variant="overline"
            className="block items-center"
          >
            <span>
              Please, ensure all requirements are installed. To get more information visit our
            </span>

            <span className="inline-block">
              <NextLink
                href="https://dbvisor.gitbook.io/"
                className="link-0 link-1 normal-case ml-3 with-document-icon"
                target="_blank"
              >
                <DocumentIcon />
                Documentation
              </NextLink>
            </span>
          </Typography>
          <Switch1
            className="self-start w-[450px] mt-3"
            selectedOption={clientValue}
            setOption={(newValue) => setClientValue(newValue)}
            options={clientSwitchOptions}
            name="searchtype"
          />
          <TabContext value={clientValue}>
            {clientInstallations.map((tab) => tabPanelTemplate(tab))}
          </TabContext>
        </div>
      </div>
    </div>
  );
}

IndexInstallation.propTypes = {};
