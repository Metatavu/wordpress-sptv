import React from 'react';
import { wp, WPBlockTypeEditParams } from 'wp';
import Icon from "./phone-service-channel-icon";
import PhoneServiceChannelComponent from './components/phone-service-channel-component';
import { SptvOptions } from './types';

declare var wp: wp;
declare var sptv: SptvOptions;
const { __ } = wp.i18n;

const { registerBlockType } = wp.blocks;

/**
 * Registers block type
 */
registerBlockType('sptv/phone-service-channel-block', {
  title: __( 'Phone service channel', 'sptv' ),
  icon: Icon,
  category: 'sptv',

  attributes: {
    "id": {
      type: 'string'
    },
    "component": {
      type: 'string'
    },
    "language": {
      type: 'string'
    }
  },

  /**
   * Block type edit method 
   */
  edit: ((params: WPBlockTypeEditParams) => {
    const { isSelected } = params;

    const getAttribute = (attribute: string): string => {
      return params.attributes[attribute];
    }

    const setAttribute = (attribute: string, value: string) => {
      const attributes: { [key: string]: string } = { }; 
      attributes[attribute] = value; 
      params.setAttributes(attributes);
    }

    if (!getAttribute("component")) {
      setAttribute("component", sptv.phoneServiceChannelBlock.components ? sptv.phoneServiceChannelBlock.components[0].slug : null);
    }

    if (!getAttribute("language")) {
      setAttribute("language", "fi");
    }

    return <PhoneServiceChannelComponent 
      editing={ isSelected } 
      channelId={ getAttribute("id") } 
      component={ getAttribute("component") } 
      language={ getAttribute("language") }
      onComponentChange={(component: string) => {
        setAttribute("component", component);
      }}
      onLanguageChange={(language: string) => {
        setAttribute("language", language);
      }}
      onChannelIdChange={(channelId: string) => {
        setAttribute("id", channelId);
      }}
      />
  }),

  /**
   * Block type save method 
   */
  save: (): any => {
    return null;
  }

});

