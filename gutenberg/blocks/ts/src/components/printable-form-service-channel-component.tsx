import React from 'react';
import { wp } from 'wp';
import PrintableFormServiceChannelInspectorControls from './printable-form-service-channel-inspector-controls';

declare var wp: wp;
const { __ } = wp.i18n;

/**
 * Interface describing component props
 */
interface Props {
  editing: boolean,
  channelId?: string,
  component?: string,
  language: string,
  onComponentChange(component: string) : void,
  onLanguageChange(language: string) : void,
  onChannelIdChange(channelId: string): void
}

/**
 * Interface describing component state
 */
interface State {
}

/**
 * Printable form service block
 */
class PrintableFormServiceChannelComponent extends React.Component<Props, State> {

  /*
   * Constructor
   * 
   * @param props props
   */
  constructor(props: Props) {
    super(props);
    this.state = { };
  }

  /**
   * Component render method
   */
  public render() {
    return (
      <div>
        { this.renderPreview() }
        { this.renderInspectorControls() }
      </div>
    );
  }

  /**
   * Renders inspector controls
   */
  private renderInspectorControls = () => {
    return (
      <PrintableFormServiceChannelInspectorControls
        editing={ this.props.editing } 
        channelId={ this.props.channelId }
        language={ this.props.language } 
        component={ this.props. component }
        onComponentChange={ this.props.onComponentChange }
        onLanguageChange={ this.props.onLanguageChange }
        onChannelIdChange={ this.props.onChannelIdChange }/>
    );
  }

  /**
   * Renders preview
   */
  private renderPreview = () => {
    return (
      <div>
        <wp.serverSideRender 
          block="sptv/printable-form-service-channel-block" 
          attributes={{
            id: this.props.channelId, 
            language: this.props.language,
            component: this.props.component
          }}/>
      </div>
    );
  }

}
export default PrintableFormServiceChannelComponent;