import React from 'react';
import { wp } from 'wp';
import { SptvOptions } from '../types';
import { VmOpenApiLocalizedListItem, V10VmOpenApiPhoneChannel } from '../ptv/model/v10/api';
import { SearchModal } from './search-modal';
import PTV from '../ptv';

declare var wp: wp;
declare var sptv: SptvOptions;
const { __ } = wp.i18n;

/**
 * Interface describing component props
 */
interface Props {
  editing: boolean,
  channelId?: string,
  language: string,
  component: string,
  onLanguageChange: (language: string) => void,
  onComponentChange: (component: string) => void,
  onChannelIdChange: (channelId: string) => void
}

/**
 * Interface describing component state
 */
interface State {
  searchOpen: boolean
}

/**
 * Phone service channel inspector controls
 */
class PhoneServiceChannelInspectorControls extends React.Component<Props, State> {

  private ptv = new PTV();

  /*
   * Constructor
   * 
   * @param props props
   */
  constructor(props: Props) {
    super(props);
    this.state = {
      searchOpen: props.editing && !props.channelId
    };
  }

  /**
   * Component did update life-cycle event
   * 
   * @param prevProps previous props
   * @param prevState previous state
   */
  public componentDidUpdate(prevProps: Props, prevState: State) {
    
  }

  /**
   * Component render method
   */
  public render() {
    const { InspectorControls } = wp.blockEditor;

    return (
      <InspectorControls>
        { this.renderLanguageSelect() }
        { this.renderComponentSelect() }
        { this.renderChangeButton() }
        { this.renderSearchModal() }
      </InspectorControls>
    );
  }

  /**
   * Renders change button if needed
   */
  private renderChangeButton() {
    return (
      <div>
        <wp.components.Button variant='primary' onClick={ this.onChangeButtonClick }>{__( 'Change phone service', 'sptv' )}</wp.components.Button>
      </div> 
    );
  }
  
  /**
   * Renders language select
   */
  private renderLanguageSelect = () => {
    const { SelectControl, Tooltip } = wp.components;

    const title = __("Language", "sptv");
    const hint = __("Select language of displayed component", "sptv");
    const options = [{
      value: "fi",
      label: __("Finnish", "sptv")
    }, {
      value: "sv",
      label: __("Swedish", "sptv")
    }, {
      value: "en",
      label: __("Enlish", "sptv")
    }];

    return (
      <div>
        <Tooltip text={ hint } >
          <label> { title } </label>
        </Tooltip>
        <SelectControl value={ this.props.language } onChange={(value: string) => this.props.onLanguageChange(value) } options={ options }></SelectControl>
      </div>
    );
  }
  
  /**
   * Renders component select
   */
  private renderComponentSelect = () => {
    const { SelectControl, Tooltip } = wp.components;

    const title = __("Component", "sptv");
    const hint = __("Select displayed component", "sptv");
    const options = sptv.phoneServiceChannelBlock.components.map((component) => {
      return {
        value: component.slug,
        label: component.name
      };
    });

    return (
      <div>
        <Tooltip text={ hint } >
          <label> { title } </label>
        </Tooltip>
        <SelectControl value={ this.props.component } onChange={(value: string) => this.props.onComponentChange(value) } options={ options }></SelectControl>
      </div>
    );
  }

  /**
   * Renders a search modal
   */
  private renderSearchModal = () => {
    return (
      <SearchModal 
        modalTitle={ __("Search Phone Services", 'sptv') }
        inputLabel={ __("Search Phone Services", "sptv") }
        inputHelp={ __("Enter the first word to search phone services", "sptv") }
        open={ this.state.searchOpen }
        getDisplayName={ this.getSearchResultDisplayName }
        doSearch={ this.doSearch }
        onSelect={ this.onSearchModalSelect }
        onClose={  this.onSearchModalClose }/> 
    );
  }

  /**
   * Executes a search
   * 
   * @param text search string
   * @returns array of phone service channels
   */
  private doSearch = async (text: string): Promise<V10VmOpenApiPhoneChannel[]> => {
    const lang = "fi";
    const type = "phone";
    const apiFetch = wp.apiFetch;
    const responseIds = await apiFetch( { path: `/sptv/search-service-channels?q=${text}&type=${type}&lang=${lang}` });
    const serviceChannels = await this.ptv.findServiceChannels(responseIds);
    const foundChannels = serviceChannels.filter(channel => channel !== undefined);
    return foundChannels;
  }

  /**
   * Returns display name for a search result
   * 
   * @param entity search result entity
   * @return display name for a search result
   */
  private getSearchResultDisplayName = (entity: V10VmOpenApiPhoneChannel) => {
    return this.getLocalizedValue(entity.serviceChannelNames, "fi", "Name");
  }

  /**
   * Returns localized value
   * 
   * @param values array containing localized values
   * @param language language
   * @param type return value with given type
   * @returns value
   */
  private getLocalizedValue(values: Array<VmOpenApiLocalizedListItem>, language: string, type: string): string | null {
    if (!values || !values.length) {
      return null;
    }

    const filtered =  values.filter((value) => (!type || value.type == type) && value.value && value.value.trim());
    
    filtered.sort((a: VmOpenApiLocalizedListItem, b: VmOpenApiLocalizedListItem) => {
      return a.language === language ? -1 : 1;
    });
    
    return filtered[0] ? filtered[0].value : null;
  }

  /**
   * Event for search modal entity select
   * 
   * @param data selected entity
   */
  private onSearchModalSelect = (data: V10VmOpenApiPhoneChannel) => {
    this.setState( { searchOpen: false } ); 
    this.props.onChannelIdChange(data.id);
  }

  /**
   * Event for search modal close
   */
  private onSearchModalClose = () => {
    this.setState( { searchOpen: false } );
  }

  /**
   * Event for change button click
   */
  private onChangeButtonClick = () => {
    this.setState({
      searchOpen: true
    });
  }

}

export default PhoneServiceChannelInspectorControls;