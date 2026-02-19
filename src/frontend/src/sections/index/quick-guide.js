import React from 'react';
import { Typography } from '@mui/material';
import NextLink from 'next/link';
import { DocumentIcon, ContactUsIcon } from 'src/elements/icons';

export function QuickGuide() {
  const setupSteps = [
    {
      step: 'Install VeilDB Agent to your server and link it to your account',
      link: 'https://veildb.gitbook.io/dbvisor-docs/user-guide/dbvisor-agent/database-management'
    },
    { step: 'Add new database and analyze it' },
    {
      step: 'Configure scheduling and rules',
      link: 'https://veildb.gitbook.io/dbvisor-docs/user-guide/dbvisor-agent'
    },
    {
      step: 'Give permissions to your team',
      link: 'https://veildb.gitbook.io/dbvisor-docs/user-guide/dbvisor-agent'
    }
  ];

  const stepList = setupSteps.map((step, index) => (
    <li key={index} className="mb-2">
      <Typography
        variant="overline"
        className="
        !text-[14px]
        !normal-case
        !font-medium
        items-center
        block
        leading-normal
        "
      >
        <span>
          {step.step}
        </span>

        {
          step.link && (
            <span className="inline-block h-[18px]">
              <NextLink
                href={step.link}
                className="
                link-0
                link-1
                normal-case
                ml-3
                with-document-icon
                inline
                float-right
                relative top-1
                "
                target="_blank"
              >
                <DocumentIcon />
                Documentation
              </NextLink>
            </span>
          )
        }
      </Typography>
    </li>
  ));

  return (
    <div className="max-w-[400px] mr-24">
      <Typography variant="h4" className="!mb-2">What is next? Quick guide</Typography>
      <ul className="list-decimal text-dbm-color-3 pl-[16px]">
        {stepList}
      </ul>

      <div
        className="flex bg-dbm-color-white rounded-lg py-[29px] px-[15px] center mt-14"
      >
        <Typography variant="h5" className="!text-[14px]">Got a question?</Typography>
        <span className="flex h-[26px]">
          <NextLink
            href="/contact"
            className="link-0 link-1 normal-case ml-3 with-document-icon"
            target="_blank"
          >
            <ContactUsIcon />
            Contact us
          </NextLink>
        </span>
      </div>
    </div>
  );
}

QuickGuide.propTypes = {};
