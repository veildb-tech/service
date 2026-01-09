import NextLink from 'next/link';
import * as React from 'react';
import { useUrl } from 'src/hooks/use-url';

function Breadcrumbs(props) {
  const { collection, className } = props;
  const urlHook = useUrl();

  return (
    collection?.length && (
    <ul className={`breadcrumbs ${className || ''}`}>
      {collection.map((item, i) => {
        const { url, title } = item;

        return (
          <li key={i} className={`breadcrumb${url ? '' : ' breadcrumb-active'}`}>
            <NextLink href={url ? urlHook.getUrl(url) : '#'}>{title}</NextLink>
          </li>
        );
      })}
    </ul>
    )
  );
}

export default Breadcrumbs;
