package playbasis.demoApp;

import playbasis.pblib.Playbasis;

import android.app.Activity;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;

public class MainActivity extends Activity
{
	public static final int MAX_OUTPUT_CHARS = 512;
	
	private InputProcessor inputProcessor;
	public static Playbasis playbasis;

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		// set the action listener for the enter key
		inputProcessor = new InputProcessor(this);
		EditText inputBox = (EditText) findViewById(R.id.inputBox);
		assert inputBox != null;
		inputBox.setOnEditorActionListener(inputProcessor);
		
		playbasis = new Playbasis();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu)
	{
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.activity_main, menu);
		return true;
	}

	// process the input and display to the output box
	public void submitClicked(View view)
	{
		EditText inputBox = (EditText) findViewById(R.id.inputBox);
		assert inputBox != null;
		String message = inputBox.getText().toString();

		AsyncWebRequestTask requestTask = new AsyncWebRequestTask(this);
		requestTask.execute(message);
		message = "Processing Request: " + message;

		TextView outputBox = (TextView) findViewById(R.id.outputBox);
		assert outputBox != null;
		String output = outputBox.getText().toString();

		output = message + "\n" + output;
		if (output.length() > 256)
			output = output.substring(0, 256);
		outputBox.setText(output);
		inputBox.setText("");
	}
}

class InputProcessor implements OnEditorActionListener
{
	private MainActivity mainActivity;

	public InputProcessor(MainActivity activity)
	{
		mainActivity = activity;
	}

	@Override
	public boolean onEditorAction(TextView v, int actionId, KeyEvent event)
	{
		boolean enterPressed = event.getAction() == KeyEvent.ACTION_DOWN && event.getKeyCode() == KeyEvent.KEYCODE_ENTER;
		if (!enterPressed || (actionId != 0 && actionId != EditorInfo.IME_ACTION_DONE
											&& actionId != EditorInfo.IME_ACTION_GO
											&& actionId != EditorInfo.IME_ACTION_NEXT 
											&& actionId != EditorInfo.IME_ACTION_SEND))
			return false; // skip event that we don't care about

		// process the input
		assert mainActivity != null;
		mainActivity.submitClicked(v);
		return true;
	}
}

class AsyncWebRequestTask extends AsyncTask<String, Void, String>
{
	private MainActivity mainActivity;

	public AsyncWebRequestTask(MainActivity activity)
	{
		mainActivity = activity;
	}

	@Override
	protected String doInBackground(String... args)
	{
		String req = args[0];
		String[] input = req.split("\\s+");
		if(input.length <= 0)
			return null;
		return Playbasis.instance.auth(input[0], input[1]);
	}

	@Override
	protected void onPostExecute(String result)
	{
		TextView outputBox = (TextView) mainActivity
				.findViewById(R.id.outputBox);
		assert outputBox != null;
		String output = outputBox.getText().toString();

		output = result + "\n" + output;
		if (output.length() > MainActivity.MAX_OUTPUT_CHARS)
			output = output.substring(0, MainActivity.MAX_OUTPUT_CHARS);
		outputBox.setText(output);
	}
}
