import React from 'react';
import { wp } from 'wp';
import { SearchInput } from './search-input';

const { __ } = wp.i18n;

declare var wp: wp;

/**
 * Interface describing search modal component props
 */
interface Props {
  open: boolean,
  modalTitle: string,
  doSearch(): Promise<any[]>,
  onClose: () => void,
  getDisplayName(entity: any): string, 
  onSelect: (service: any) => void
}

/**
 * Interface describing search modal component state
 */
interface State {
  entities: any[], 
  searching: boolean,
  hoverIndex: number,
}

/**
 * Search modal component
 */
export class OrganizationModal extends React.Component<Props, State> {

  /**
   * Constructor
   * 
   * @param props props
   */
  constructor(props: Props) {
    super(props);

    this.state = {
      entities: [], 
      searching: false,
      hoverIndex: -1,
    };
  }

  /**
   * Component did mount life-cycle event
   * 
   */
  public componentDidMount = async() => {
    await this.search()
  }

  /**
   * Component render method
   */
  public render() {
    if (!this.props.open) {
      return null;
    }

    return (
      <wp.components.Modal style={{ minWidth: "60%" }} title={ this.props.modalTitle } onRequestClose={ () => this.props.onClose() }>
        <div style={{ height: "300px", overflowY: "auto" }}>
        {
          this.state.searching ? (<wp.components.Placeholder style={{ height: "300px" }}><wp.components.Spinner /></wp.components.Placeholder> ) : this.state.entities.map((entity, index) => {
            return <div 
              onMouseOver={ () => this.setState({ hoverIndex: index }) } 
              onClick = { () => this.onSelect(entity) }
              style={{ fontWeight: this.state.hoverIndex == index ? "bold": "normal", cursor: "pointer", paddingTop: "5px", paddingBottom: "5px" }} 
              key={entity.id}>{this.props.getDisplayName(entity)}</div>
          })
        }
        </div>
      </wp.components.Modal>
    );
  }

  /**
   * Executes a search with given string
   */
  private search = async () => {
    this.setState({ 
      searching: true 
    });

    let result = await this.props.doSearch();

    this.setState({ 
      searching: false, 
      entities: result,
    });
  }

  /**
   * Event handler for handling search input select
   * 
   * @param data selected data
   */
  private onSelect(data: any) {
    this.props.onSelect(data);
  }
}